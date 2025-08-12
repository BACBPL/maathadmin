<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    /**
     * Handle the response after a user is authenticated.
     */
    public function toResponse($request): RedirectResponse
    {
        // 1) Store the usertype into session
        $request->session()->put('usertype', Auth::user()->user_type);

        // 2) Redirect to your dashboard (or wherever)
        return redirect()->route('panel.dashboard');
    }
}
