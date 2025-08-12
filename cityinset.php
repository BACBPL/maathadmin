<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Paste your JSON here:
$json = '[{"Message":"Number of Post office(s) found:18","Status":"Success","PostOffice":[{"Name":"Bowbazar (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata Central","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700012"},{"Name":"Chittaranjan Avenue (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata Central","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700073"},{"Name":"College Square (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata Central","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700007"},{"Name":"Jairampur (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700061"},{"Name":"K.P.Bazar (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700082"},{"Name":"Kalagachia (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700063"},{"Name":"Kasba (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700042"},{"Name":"Kolkata Airport","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata North","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700052"},{"Name":"Kolkata Airport  Po","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata North","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700052"},{"Name":"Kolkata Armed Police","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata North","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700002"},{"Name":"Kolkata Mint","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700053"},{"Name":"Kolkata University","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata Central","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700073"},{"Name":"Lalbazar (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Kolkata Central","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700001"},{"Name":"Netaji Nagar (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700040"},{"Name":"Rabindra Nagar (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700018"},{"Name":"Sahanagar (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700026"},{"Name":"Sonai (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700043"},{"Name":"Viveknagar (Kolkata)","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"West Bengal","District":"Kolkata","Division":"Calcutta South","Region":"Calcutta","State":"West Bengal","Country":"India","Pincode":"700075"}]}]';

$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid JSON\n");
}

$postOffices = $data[0]['PostOffice'] ?? [];

foreach ($postOffices as $po) {
    $city    = $po['Region'] ?? null;
    $pincode = $po['Pincode'] ?? null;

    if ($city && $pincode) {
        DB::table('city_pincodes')->updateOrInsert(
            ['city' => $city, 'pincode' => $pincode],
            ['created_at' => now(), 'updated_at' => now()]
        );
    }
}

echo "Inserted/updated " . count($postOffices) . " records.\n";
