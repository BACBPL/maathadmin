<?php

// app/Http/Controllers/Panel/VendorAreaListController.php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\VendorArea;

class VendorAreaListController extends Controller
{
    public function index()
    {
        $areas = VendorArea::with('vendor')
            ->orderBy('verified')      // pending first
            ->orderBy('id', 'desc')
            ->get();

        $rows = $areas->map(function ($area) {
            $v = $area->vendor;

            // PERSONAL name only from vendor_details table
            $vendorName = collect([
                $v->name ?? null,             // vendor_details.name
                $v->owner_name ?? null,       // if you have it
                $v->contact_person ?? null,   // if you have it
            ])->first(fn ($x) => filled($x)) ?? '—';

            // "700001-700002-700003" -> "700001, 700002, 700003"
            $pins = preg_split('/-+/', $area->service_area ?? '', -1, PREG_SPLIT_NO_EMPTY);
            $pins = collect($pins)
                ->filter(fn ($p) => preg_match('/^\d{6}$/', $p))
                ->unique()->sort()->values()->all();

            return [
                'id'        => $area->id,
                'vendor'    => $vendorName,
                'pincodes'  => $pins ? implode(', ', $pins) : '—',
                'verified'  => (bool) $area->verified,
            ];
        });

        return view('pages.panel.vendor_wise_area', compact('rows'));
    }

    public function approve(VendorArea $area)
    {
        $area->update(['verified' => true]);
        return back()->with('success', 'Vendor area approved.');
    }
}

