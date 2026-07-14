<?php

namespace Modules\Jetstream\Actions;

use Carbon\Carbon;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;
use Modules\User\Enums\UserLevel;

class RedirectAfterLogin implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();
        session()->regenerate();
        if (!$user->active || ($user->expired_at && $user->expired_at < Carbon::now())) {
            return redirect()->route('logout')->with('error', 'شما دسترسی لازم را ندارید.');
        }
        return match ($user->level) {
            UserLevel::ADMIN->value => redirect()->intended(route('admin.dashboard')),
            UserLevel::SELLER->value => redirect()->intended(route('seller.dashboard')),
            default => redirect()->route('logout')->with('error', 'شما دسترسی لازم را ندارید.')
        };
    }
}
