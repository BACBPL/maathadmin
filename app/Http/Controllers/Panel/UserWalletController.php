<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\VendorDetail;
use App\Models\WalletDetail;
use App\Models\WalletTransaction;

class UserWalletController extends Controller
{
    public function index()
    {
        $vendors = VendorDetail::with('walletDetail')->get();
        return view('pages.panel.balance', compact('vendors'));
    }

    public function store(Request $request, VendorDetail $vendor) // matches {vendor}
    {
        $data = $request->validate(['amount' => 'required|numeric|min:0.01']);

        DB::transaction(function () use ($vendor, $data) {
            $wallet = WalletDetail::firstOrCreate(
                ['user' => $vendor->id],          // use your FK name here
                ['wallet_balance' => 0]
            );
            $wallet->increment('wallet_balance', $data['amount']);

            WalletTransaction::create([
                'f_id' => 1,
                't_id' => $vendor->id,
                'amount' => $data['amount'],
            ]);
        });

        return redirect()->route('panel.user.balance')
            ->with('success', "Added ₹" . number_format($data['amount'], 2) . " to {$vendor->name}");
    }
}
