<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VendorProductController extends Controller
{
    public function create()
    {
        $categories = \App\Models\Category::where('based', 'product')
            ->orderBy('name')
            ->get();

        return view('pages.panel.product_create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'sku'         => ['nullable','string','max:100','unique:products,sku'],
            'price'       => ['required','numeric','min:0'],
            'sale_price'  => ['nullable','numeric','lte:price','min:0'],
            'stock_qty'   => ['required','integer','min:0'],
            'status'      => ['required','in:draft,active,inactive'],
            'description' => ['nullable','string'],

            // single dropdown category
            'category_id' => [
                'required',
                Rule::exists('categories','id')->where(fn($q) => $q->where('based','product')),
            ],

            // accept single or multiple files
            'images'      => ['required'],
            'images.*'    => ['image','mimes:jpeg,png,jpg,gif,svg,webp','max:4096'],

            // specs + extra price per spec
            'spec_key'       => ['array'],
            'spec_key.*'     => ['nullable','string','max:100'],
            'spec_value'     => ['array'],
            'spec_value.*'   => ['nullable','string','max:500'],
            'spec_price'     => ['array'],
            'spec_price.*'   => ['nullable','numeric','min:0'], // empty => 0.00
        ]);

        $vendorId = auth()->user()->two_factor_recovery_codes;

        $slugBase = Str::slug($data['title']);
        $slug = $this->uniqueSlug($slugBase);

        DB::beginTransaction();
        try {
            $product = Product::create([
                'vendor_id'   => $vendorId,
                'title'       => $data['title'],
                'slug'        => $slug,
                'sku'         => $data['sku'] ?? null,
                'price'       => $data['price'],
                'sale_price'  => $data['sale_price'] ?? null,
                'stock_qty'   => $data['stock_qty'],
                'status'      => $data['status'],
                'description' => $data['description'] ?? null,
                'weight'      => $request->input('weight'),
                'length'      => $request->input('length'),
                'width'       => $request->input('width'),
                'height'      => $request->input('height'),
            ]);

            // attach category
            $product->categories()->sync([$data['category_id']]);

            // specs (key/value/extra_price)
            $specKeys    = $request->input('spec_key', []);
            $specVals    = $request->input('spec_value', []);
            $specPrices  = $request->input('spec_price', []);
            foreach ($specKeys as $i => $key) {
                $k = trim((string)($key ?? ''));
                $v = trim((string)($specVals[$i] ?? ''));
                $p = $specPrices[$i] ?? null;
                $p = is_numeric($p) ? max(0, (float)$p) : 0.00;

                // save a row if anything provided (key/value) or price > 0
                if ($k !== '' || $v !== '' || $p > 0) {
                    $spec = new ProductSpec();
                    $spec->product_id  = $product->id;
                    $spec->spec_key    = $k !== '' ? $k : '-';
                    $spec->spec_value  = $v !== '' ? $v : null;
                    $spec->extra_price = $p;               // << new
                    $spec->save();                          // avoid mass-assignment issues
                }
            }

            // images (normalize to array)
            $filesRaw = $request->file('images');
            $files = is_array($filesRaw) ? $filesRaw : array_filter([$filesRaw]);

            $dest = public_path("assets/img/products/{$product->id}");
            File::ensureDirectoryExists($dest, 0755, true);

            foreach ($files as $index => $file) {
                if (!$file) continue;
                $base     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeBase = Str::slug($base) ?: 'image';
                $ext      = $file->getClientOriginalExtension();
                $filename = now()->timestamp . "_{$index}_{$safeBase}.{$ext}";
                $file->move($dest, $filename);

                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => "assets/img/products/{$product->id}/{$filename}",
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('panel.vendor.product.add')
                ->with('success', __('Product created successfully.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->withErrors([
                'general' => __('Something went wrong while saving the product.'),
            ]);
        }
    }

    

    private function uniqueSlug(string $base): string
    {
        $slug = $base ?: 'product';
        $original = $slug;
        $i = 1;
        while (\App\Models\Product::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }
        return $slug;
    }
        public function index()
    {
        $vendorId = auth()->user()->two_factor_recovery_codes;
        $products = Product::with(['primaryImage','categories'])
            ->where('vendor_id', $vendorId)
            ->latest()->paginate(15);

        return view('pages.panel.product_index', compact('products'));
    }

    /** Show edit form (name & sku read-only) */
    public function edit(Product $product)
    {
       // $this->ensureOwner($product);
        $categories = \App\Models\Category::where('based','product')->orderBy('name')->get();
        $product->load(['images','specs','categories']);
        return view('pages.panel.product_edit', compact('product','categories'));
    }

    /** Apply edits (except name & sku) */
    public function update(Request $request, Product $product)
    {
        //$this->ensureOwner($product);

        $data = $request->validate([
            'price'       => ['required','numeric','min:0'],
            'sale_price'  => ['nullable','numeric','lte:price','min:0'],
            'stock_qty'   => ['required','integer','min:0'],
            'status'      => ['required','in:draft,active,inactive'],
            'description' => ['nullable','string'],
            'category_id' => ['required', Rule::exists('categories','id')->where(fn($q)=>$q->where('based','product'))],

            // specs
            'spec_id'      => ['array'],
            'spec_id.*'    => ['nullable','integer','exists:product_specs,id'],
            'spec_key'     => ['array'],
            'spec_key.*'   => ['nullable','string','max:100'],
            'spec_value'   => ['array'],
            'spec_value.*' => ['nullable','string','max:500'],
            'spec_price'   => ['array'],
            'spec_price.*' => ['nullable','numeric','min:0'],
            'delete_spec'  => ['array'],
            'delete_spec.*'=> ['integer','exists:product_specs,id'],

            // images
            'images'       => ['nullable'],
            'images.*'     => ['image','mimes:jpeg,png,jpg,gif,svg,webp','max:4096'],
            'remove_images'=> ['array'],
            'remove_images.*' => ['integer','exists:product_images,id'],
            'primary_image_id' => ['nullable','integer',
                Rule::exists('product_images','id')->where(fn($q)=>$q->where('product_id',$product->id))
            ],
            'make_first_new_primary' => ['nullable','boolean'],

            // physical
            'weight' => ['nullable','numeric','min:0'],
            'length' => ['nullable','numeric','min:0'],
            'width'  => ['nullable','numeric','min:0'],
            'height' => ['nullable','numeric','min:0'],
        ]);

        DB::beginTransaction();
        try {
            // core updates (NOT changing title/sku)
            $product->update([
                'price' => $data['price'], 'sale_price' => $data['sale_price'] ?? null,
                'stock_qty' => $data['stock_qty'], 'status' => $data['status'],
                'description' => $data['description'] ?? null,
                'weight' => $request->input('weight'), 'length' => $request->input('length'),
                'width' => $request->input('width'), 'height' => $request->input('height'),
            ]);

            // category
            $product->categories()->sync([$data['category_id']]);

            // delete marked specs
            $deleteSpecIds = $request->input('delete_spec', []);
            if ($deleteSpecIds) {
                ProductSpec::where('product_id',$product->id)->whereIn('id',$deleteSpecIds)->delete();
            }

            // upsert specs
            $ids  = $request->input('spec_id', []);
            $keys = $request->input('spec_key', []);
            $vals = $request->input('spec_value', []);
            $prs  = $request->input('spec_price', []);
            $count = max(count($keys), count($vals), count($ids), count($prs));
            for ($i=0; $i<$count; $i++) {
                $sid = $ids[$i] ?? null;
                $k = trim((string)($keys[$i] ?? ''));
                $v = trim((string)($vals[$i] ?? ''));
                $p = $prs[$i] ?? null; $p = is_numeric($p) ? max(0,(float)$p) : 0.00;

                // skip if row is blank and price == 0
                if (!$sid && $k==='' && $v==='' && $p==0.00) continue;

                if ($sid && !in_array($sid, $deleteSpecIds)) {
                    ProductSpec::where('id',$sid)->where('product_id',$product->id)->update([
                        'spec_key' => $k !== '' ? $k : '-',
                        'spec_value' => $v !== '' ? $v : null,
                        'extra_price' => $p,
                    ]);
                } elseif (!$sid) {
                    $spec = new ProductSpec();
                    $spec->product_id = $product->id;
                    $spec->spec_key = $k !== '' ? $k : '-';
                    $spec->spec_value = $v !== '' ? $v : null;
                    $spec->extra_price = $p;
                    $spec->save();
                }
            }

            // remove images
            $removeIds = $request->input('remove_images', []);
            if ($removeIds) {
                $imgs = ProductImage::where('product_id',$product->id)->whereIn('id',$removeIds)->get();
                foreach ($imgs as $img) {
                    $full = public_path($img->path);
                    if (File::exists($full)) File::delete($full);
                    $img->delete();
                }
            }

            // upload new images
            $newFirstId = null;
            $filesRaw = $request->file('images'); $files = is_array($filesRaw) ? $filesRaw : array_filter([$filesRaw]);
            if ($files) {
                $dest = public_path("assets/img/products/{$product->id}"); File::ensureDirectoryExists($dest, 0755, true);
                foreach ($files as $idx => $file) {
                    if (!$file) continue;
                    $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
                    $ext  = $file->getClientOriginalExtension();
                    $filename = now()->timestamp . "_new{$idx}_{$base}.{$ext}";
                    $file->move($dest, $filename);
                    $img = ProductImage::create([
                        'product_id'=>$product->id, 'path'=>"assets/img/products/{$product->id}/{$filename}",
                        'is_primary'=>false, 'sort_order'=> (int) (ProductImage::where('product_id',$product->id)->max('sort_order')+1),
                    ]);
                    if ($newFirstId === null) $newFirstId = $img->id;
                }
            }

            // set primary image
            $primaryId = $request->input('primary_image_id');
            if ($primaryId) {
                ProductImage::where('product_id',$product->id)->update(['is_primary'=>false]);
                ProductImage::where('product_id',$product->id)->where('id',$primaryId)->update(['is_primary'=>true]);
            } elseif ($newFirstId && $request->boolean('make_first_new_primary')) {
                ProductImage::where('product_id',$product->id)->update(['is_primary'=>false]);
                ProductImage::where('id',$newFirstId)->update(['is_primary'=>true]);
            } else {
                // ensure one primary exists
                if (!ProductImage::where('product_id',$product->id)->where('is_primary',true)->exists()) {
                    $first = ProductImage::where('product_id',$product->id)->first();
                    if ($first) $first->update(['is_primary'=>true]);
                }
            }

            DB::commit();
            return redirect()->route('panel.vendor.product.edit.form',$product)->with('success', __('Product updated successfully.'));
        } catch (\Throwable $e) {
            DB::rollBack(); report($e);
            return back()->withInput()->withErrors(['general'=>__('Update failed. Please try again.')]);
        }
    }

    // helpers
    private function ensureOwner(Product $product): void
    {
        if ($product->vendor_id !== auth()->user()->two_factor_recovery_codes) abort(403);
    }

   public function vendorIndex(Request $request)
{
    // All vendor IDs that have products
    $vendorIds = \App\Models\Product::query()
        ->distinct()
        ->pluck('vendor_id')
        ->filter()   // drop null/empty
        ->values();

    if ($vendorIds->isEmpty()) {
        // Nothing to show – render an empty page gracefully
        return view('pages.panel.vendor_products', [
            'vendorList'        => collect(),
            'productsByVendor'  => collect(),
            'activeVendorId'    => null,
        ]);
    }

    // Pull names from vendor_details
    $vendorRecords = \App\Models\VendorDetail::whereIn('id', $vendorIds)->get()->keyBy('id');

    // Build a simple list with fallbacks (Vendor #ID if missing row)
    $vendorList = $vendorIds->map(function ($id) use ($vendorRecords) {
        $name = optional($vendorRecords->get($id))->name ?? "Vendor #{$id}";
        return (object) ['id' => (int)$id, 'name' => $name];
    })->sortBy('name')->values();

    // Eager-load all products grouped by vendor
    $productsByVendor = \App\Models\Product::with(['images', 'categories'])
        ->whereIn('vendor_id', $vendorIds)
        ->orderByDesc('created_at')
        ->get()
        ->groupBy('vendor_id');

    // Which tab is active? Use ?vendor=ID or first vendor by default
    $activeVendorId = (int)($request->query('vendor') ?: $vendorList->first()->id);

    return view('pages.panel.vendor_products', compact('vendorList', 'productsByVendor', 'activeVendorId'));
}
    
}
