<?php


namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\VendorDetail;
use App\Models\VendorService;
use Illuminate\Http\Request;

class VendorServiceController extends Controller
{
    public function edit()
    {
       
        if (auth()->user()->user_type !== 'vendor') {
            abort(403, 'Only vendors can access this page.');
        }

       $categories = Category::with('subCategories')->orderBy('name')->get();

        $stored = VendorService::where('vendor_id', auth()->user()->two_factor_recovery_codes)->value('subcategory_ids');
        $selected = $stored ? array_filter(explode('-', $stored)) : [];

        return view('pages.panel.services', compact('categories', 'selected'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'subcategories'   => ['array'],
            'subcategories.*' => ['integer', 'exists:sub_categories,id'],
        ]);

        $ids = $data['subcategories'] ?? [];
        $ids = array_map('intval', array_values(array_unique($ids)));
        sort($ids);

        $joined = implode('-', $ids);

        VendorService::updateOrCreate(
            ['vendor_id' => auth()->user()->two_factor_recovery_codes],
            ['subcategory_ids' => $joined]
        );

        return redirect()
            ->route('panel.vendor.services.edit')
            ->with('success', 'Services saved successfully.');
    }
}

