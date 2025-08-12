<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\VendorDetail;
use Illuminate\Support\Facades\Hash;
use App\Models\VendorDocument;

class VendorController extends Controller
{
    /**
     * STEP 1: Show the form for basic details.
     */
    public function createBasic()
    {
        // both steps live in pages/panel/vendor/create.blade.php
        return view('pages.panel.create_basic');
    }

    /**
     * STEP 1: Validate & save basic details, then redirect to step 2.
     */
    public function storeBasic(Request $r)
    {
        $data = $r->validate([
            'name'           => 'required|string|max:255',
            'address'        => 'required|string',
            'email'          => 'required|string',
            'phone'          => 'required|string|max:20',
            'company_name'   => 'required|string|max:255',
            'personal_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($file = $r->file('personal_image')) {
            $base     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time().'_'.Str::slug($base).'.'.$file->getClientOriginalExtension();
            $dest     = public_path('assets/img/vendors/personal');
            File::ensureDirectoryExists($dest, 0755, true);
            $file->move($dest, $filename);
            $data['photo'] = "assets/img/vendors/personal/{$filename}";
        }
           $data['password'] = Hash::make("12345678");
        $vendor = VendorDetail::create($data);

        // Redirect to the same blade, now in "Step 2" mode because $vendor exists
        return redirect()
            ->route('panel.vendor.docs.create', $vendor);
    }

    /**
     * STEP 2: Show the same form, but prepped for documents.
     */
    public function createDocs(VendorDetail $vendor)
    {
        return view('pages.panel.create_basic', compact('vendor'));
    }

    /**
     * STEP 2: Validate & save documents, then finish.
     */
    public function storeDocs(Request $r, VendorDetail $vendor)
    {
        $data = $r->validate([
            'aadhar_number'        => 'required|string',
            'aadhar_image'         => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_number'           => 'required|string',
            'pan_image'            => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'trade_license_number' => 'required|string',
            'trade_license_image'  => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gst_number'           => 'required|string',
            'gst_image'            => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        foreach (['aadhar','pan','trade_license','gst'] as $field) {
            $file = $r->file("{$field}_image");
            $name = Str::slug($data["{$field}_number"]);
            $fn   = time()."_{$field}_{$name}.{$file->extension()}";
            $dest = public_path("assets/img/vendors/{$field}");
            File::ensureDirectoryExists($dest, 0755, true);
            $file->move($dest, $fn);
            $data["{$field}_image"] = "assets/img/vendors/{$field}/{$fn}";
        }

        VendorDocument::create(array_merge(
            ['vendor_detail_id' => $vendor->id],
            $data
        ));

        return redirect()
            ->route('panel.dashboard')
            ->with('success','Vendor created successfully.');
    }
}
