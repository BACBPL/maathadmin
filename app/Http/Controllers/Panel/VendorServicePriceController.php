<?php


namespace App\Http\Controllers\Panel;

use App\Models\VendorService;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\VendorSubcategoryPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorServicePriceController extends Controller
{
    public function index()
    {
        // if you auth vendors differently, swap this to Auth::guard('vendor')->id();
        $vendorId = auth()->user()->two_factor_recovery_codes;

        // gather all subcategory IDs for this vendor from vendor_services (supports "1-2-3")
        $services = VendorService::where('vendor_id', $vendorId)->get();

        $subcategoryIds = $services->flatMap(function ($vs) {
            return $vs->subcategoryIdArray();   // <-- your method
        })->unique()->values();

        $items = collect();

        if ($subcategoryIds->isNotEmpty()) {
            $subcats = SubCategory::with('category')
                ->whereIn('id', $subcategoryIds)
                ->get();

            $existingPrices = VendorSubcategoryPrice::where('vendor_id', $vendorId)
                ->whereIn('subcategory_id', $subcategoryIds)
                ->pluck('price', 'subcategory_id');

            $items = $subcats->map(function ($sc) use ($existingPrices) {
                return [
                    'id'          => $sc->id,
                    'subcategory' => $sc->name,
                    'category'    => optional($sc->category)->name ?? '—',
                    'price'       => $existingPrices[$sc->id] ?? null,
                ];
            })->sortBy(['category','subcategory'])->values();
        }

        return view('pages.panel.price', compact('items'));
    }

    public function store(Request $request)
    {
        $vendorId = auth()->user()->two_factor_recovery_codes;

        $data = $request->validate([
        'price'   => ['required','array'],
        'price.*' => ['nullable','numeric','min:0'],
    ]);

    DB::transaction(function () use ($vendorId, $data) {
        foreach ($data['price'] as $subcategoryId => $priceInput) {
            if ($priceInput === null || $priceInput === '') {
                // no create/delete; just skip blanks
                continue;
            }

            $subcategoryId = (int) $subcategoryId;
            $newPrice = round((float) $priceInput, 2);

            // fetch existing row ONLY (no create)
            $row = VendorSubcategoryPrice::where('vendor_id', $vendorId)
                ->where('subcategory_id', $subcategoryId)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                // you said: no new row → skip if not found
                continue;
            }

            $oldPrice = round((float) $row->price, 2);

            // price changed → update + reset status to 0
            if (abs($newPrice - $oldPrice) > 0.00001) {
                $row->update([
                    'price'  => $newPrice,
                    'status' => 0,
                ]);
            }
        }
    });

    return back()->with('success', 'Prices saved successfully.');
    }
}

