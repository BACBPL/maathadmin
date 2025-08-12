<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\LoginResponse as CustomLoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\VendorDetail;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Fortify::loginView(fn() => view('pages.auth.login'));
        Fortify::registerView(fn() => view('pages.auth.register'));
        Fortify::verifyEmailView(fn() => view('pages.auth.verify-email'));
        Fortify::requestPasswordResetLinkView(fn() => view('pages.auth.request-password-reset'));
        Fortify::resetPasswordView(fn() => view('pages.auth.reset-password'));

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', fn(Request $r) => Limit::perMinute(5)->by((string)$r->email.$r->ip()));
        RateLimiter::for('two-factor', fn(Request $r) => Limit::perMinute(5)->by($r->session()->get('login.id')));

        $this->app->singleton(LoginResponseContract::class, CustomLoginResponse::class);

        // Custom auth: check users, then vendor_details; for vendor, create/fetch a shadow user.
        Fortify::authenticateUsing(function (Request $request) {
            if ($u = User::where('email', $request->email)->first()) {
                if (Hash::check($request->password, $u->password)) {
                    $request->session()->put('auth_source', 'user');
                    return $u;
                }
            }

            if ($v = VendorDetail::where('email', $request->email)->first()) {
                if (Hash::check($request->password, $v->password)) {
                    $shadow = User::firstOrCreate(
    ['email' => $v->email], // Search criteria
    [
        'name'                     => $v->name ?? ('Vendor ' . $v->id),
        'password'                 => $v->password, // Must already be hashed
        'user_type'                 => 'vendor',
        'two_factor_recovery_codes' => $v->id
    ]
);
                    $request->session()->put('auth_source', 'vendor');
                    return $shadow;
                }
            }

            return null;
        });
    }
}
