<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryController extends Controller
{
    /** Show the form & list in create mode */
    public function create()
    {
        $categories = Category::all();
        return view('pages.panel.create_cat', compact('categories'));
    }

    /** Persist a new category */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($file = $request->file('image')) {
            $name     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $slug     = Str::slug($name);
            $filename = time() . "_{$slug}.{$file->getClientOriginalExtension()}";
            $dest     = public_path('assets/img/categories');
            File::ensureDirectoryExists($dest, 0755, true);
            $file->move($dest, $filename);
            $data['image'] = "assets/img/categories/{$filename}";
        }

        Category::create($data);

        return redirect()
            ->route('panel.create_cat')
            ->with('success', __('Category created successfully'));
    }

    /** Show the form & list in edit mode */
    public function edit(Category $category)
    {
        //dd($category);
        $categories   = Category::all();
        $editCategory = $category;
        return view('pages.panel.create_cat', compact('categories','editCategory'));
    }

    /** Persist updates to an existing category */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($file = $request->file('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $name     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $slug     = Str::slug($name);
            $filename = time() . "_{$slug}.{$file->getClientOriginalExtension()}";
            $dest     = public_path('assets/img/categories');
            File::ensureDirectoryExists($dest, 0755, true);
            $file->move($dest, $filename);
            $data['image'] = "assets/img/categories/{$filename}";
        }

        $category->update($data);

        return redirect()
            ->route('panel.create_cat')
            ->with('success', __('Category updated successfully'));
    }

    /** Delete a category and its image */
    public function destroy(Category $category)
    {
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }
        $category->delete();

        return redirect()
            ->route('panel.category.create')
            ->with('success', __('Category deleted'));
    }
}