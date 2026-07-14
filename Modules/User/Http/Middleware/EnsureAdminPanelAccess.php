<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\ApiResponseTrait;
use Modules\User\Enums\UserLevel;

class EnsureAdminPanelAccess
{
    use ApiResponseTrait;
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->respondError('کاربر یافت نشد', 401);
        }

        if (in_array($user->level, [UserLevel::ADMIN->value, UserLevel::SALES_OPERATOR->value])) {
            return $next($request);
        }
        return $this->respondError('شما دسترسی به پنل مدیریت ندارید.', 403);
    }
}
