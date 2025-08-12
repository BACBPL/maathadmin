<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\VendorService;
use App\Models\SubCategory;
use Illuminate\Support\Arr;

class VendorWiseServiceController extends Controller
{
   public function index()
{
    $services = \App\Models\VendorService::with('vendor')->get();

    $allIds = collect($services)
        ->flatMap(fn($s) => $s->subcategoryIdArray())
        ->unique()->values();

    $subcats = \App\Models\SubCategory::with('category')
        ->whereIn('id', $allIds)
        ->get()->keyBy('id');

    $rows = [];

    foreach ($services as $svc) {
        $v = $svc->vendor;

        // ONLY company/business; never personal name
        $vendorName = collect([
    $v->name ?? null,            // vendor_details.name
    $v->owner_name ?? null,      // optional: vendor_details.owner_name
    $v->contact_person ?? null,  // optional: vendor_details.contact_person
    optional($v->user)->name,    // users.name as fallback
])->first(fn ($x) => filled($x)) ?? '—';
        // If you want to skip rows without a business name:
        // if (blank($vendorName)) { continue; }

        $catNames = [];
        $subNames = [];

        foreach ($svc->subcategoryIdArray() as $sid) {
            $sc = $subcats->get($sid);
            if (!$sc) continue;
            if (!empty($sc->name))            $subNames[] = $sc->name;
            if (!empty($sc->category->name))  $catNames[] = $sc->category->name;
        }

        $catNames = collect($catNames)->unique()->sort()->values()->all();
        $subNames = collect($subNames)->unique()->sort()->values()->all();

        $rows[] = [
            'vendor'        => $vendorName,                  // never personal
            'categories'    => implode(', ', $catNames) ?: '—',
            'subcategories' => implode(', ', $subNames) ?: '—',
        ];
    }

    $rows = collect($rows)->sortBy('vendor')->values();

    return view('pages.panel.vendor_wise_service', ['rows' => $rows]);
}

}
