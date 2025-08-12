<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    /** show “create + list” */
    public function create()
    {
        $categories    = Category::all();
        $subcategories = SubCategory::with('category')->get();
        return view('pages.panel.create_subcat', compact('categories','subcategories'));
    }

    /** store new sub-category */
    public function store(Request $r)
    {
        $data = $r->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($f = $r->file('image')) {
            $base     = pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME);
            $fn       = time().'_'.Str::slug($base).'.'.$f->getClientOriginalExtension();
            $dest     = public_path('assets/img/subcategories');
            File::ensureDirectoryExists($dest,0755,true);
            $f->move($dest, $fn);
            $data['image'] = "assets/img/subcategories/{$fn}";
        }

        SubCategory::create($data);

        return redirect()->route('panel.subcategory.create')
                         ->with('success','Sub-Category created.');
    }

    /** show “edit + list” (reuses same view) */
    public function edit(SubCategory $subcategory)
    {
        $categories    = Category::all();
        $subcategories = SubCategory::with('category')->get();
        $editSub       = $subcategory;
        return view('pages.panel.create_subcat', compact('categories','subcategories','editSub'));
    }

    /** persist update */
    public function update(Request $r, SubCategory $subcategory)
    {
        $data = $r->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($f = $r->file('image')) {
            if ($subcategory->image && File::exists(public_path($subcategory->image))) {
                File::delete(public_path($subcategory->image));
            }
            $base     = pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME);
            $fn       = time().'_'.Str::slug($base).'.'.$f->getClientOriginalExtension();
            $dest     = public_path('assets/img/subcategories');
            File::ensureDirectoryExists($dest,0755,true);
            $f->move($dest, $fn);
            $data['image'] = "assets/img/subcategories/{$fn}";
        }

        $subcategory->update($data);

        return redirect()->route('panel.subcategory.create')
                         ->with('success','Sub-Category updated.');
    }

    /** delete */
    public function destroy(SubCategory $subcategory)
    {
        if ($subcategory->image && File::exists(public_path($subcategory->image))) {
            File::delete(public_path($subcategory->image));
        }
        $subcategory->delete();

        return redirect()->route('panel.subcategory.create')
                         ->with('success','Sub-Category deleted.');
    }
}
