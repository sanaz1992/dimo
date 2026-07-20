<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Enums\UserLevel;

class EnsureSellerPanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            abort(401);
        }

        if ($user->level != UserLevel::SELLER->value) {
            abort(403, 'شما دسترسی به پنل فروشنده ندارید.');
        }
        // if (!$user->hasAnyRole([
        //     'company_manager',
        //     'company_assistance',
        //     'hall_manager',
        // ])) {
        //     abort(403, 'شما دسترسی به پنل مدیریت ندارید.');
        // }

        return $next($request);
    }
}
