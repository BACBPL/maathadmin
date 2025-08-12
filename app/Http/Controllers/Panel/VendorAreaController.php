<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CityPincode;
use App\Models\VendorArea;
use App\Models\VendorDetail;
use Illuminate\Http\Request;

class VendorAreaController extends Controller
{
   
    public function edit()
    {
        if (auth()->user()->user_type !== 'vendor') {
            abort(403, 'Only vendors can access this page.');
        }

        $pincodes = CityPincode::pluck('pincode')->unique()->sort()->values();

        // Fetch saved PINs (hyphen-separated) and convert to array
        $saved = VendorArea::where('v_id', auth()->user()->two_factor_recovery_codes)->value('service_area');
        $selected = $saved ? array_filter(explode('-', $saved)) : [];

        return view('pages.panel.service_area', [
            'pincodes' => $pincodes,
            'selected' => $selected,
        ]);
    }

    public function update(Request $request)
    {
        if (auth()->user()->user_type !== 'vendor') {
            abort(403, 'Only vendors can access this page.');
        }

        $data = $request->validate([
            'pincodes'   => ['array'],
            'pincodes.*' => ['regex:/^\d{6}$/', 'exists:city_pincodes,pincode'],
        ]);

        $pins = $data['pincodes'] ?? [];
        $pins = array_values(array_unique($pins));
        sort($pins);

        $joined = count($pins) ? implode('-', $pins) : null;

        VendorArea::updateOrCreate(
            ['v_id' => auth()->user()->two_factor_recovery_codes],
            ['service_area' => $joined]
        );

        return redirect()
            ->route('panel.vendor.services.area')
            ->with('success', 'Service area saved successfully.');
    }
}
