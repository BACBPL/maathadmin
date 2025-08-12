<?php


namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\VendorDetail;
use App\Models\VendorSubcategoryPrice;
use Illuminate\Http\Request;

class VendorPriceReviewController extends Controller
{
    public function index(Request $request)
    {
        $vendors = VendorDetail::orderBy('name')->get(['id','name']);

        $selectedVendorId = (int) $request->query('vendor_id', $vendors->first()->id ?? 0);

        $rows = collect();
        if ($selectedVendorId) {
            $rows = VendorSubcategoryPrice::with(['vendor:id,name','subcategory:id,name'])
                ->where('vendor_id', $selectedVendorId)
                ->where('status', 0)
                ->orderByDesc('id')
                ->get();
        }

        return view('pages.panel.priceappr', compact('vendors','selectedVendorId','rows'));
    }

    public function approve(Request $request, VendorSubcategoryPrice $price)
    {
        // (optional) authorize here

        $price->update(['status' => 1]);

        // respond JSON for AJAX
        return response()->json(['ok' => true, 'id' => $price->id]);
    }
}

