<?php

namespace Modules\Core\Http\Middlewares;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

// App Facade رو اضافه کن

class SetApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // اگر زبان در هدر request ارسال شده بود، از اون استفاده کن
        // مثلاً هدر 'X-Localization': 'fa'
        if ($request->hasHeader('X-Localization')) {
            App::setLocale($request->header('X-Localization'));
        }
        // یا اگر زبان از کاربر لاگین شده در دسترسه
        elseif (auth()->check() && auth()->user()->language) {
            App::setLocale(auth()->user()->language);
        }
        // در غیر این صورت، از زبان پیش‌فرض اپلیکیشن استفاده کن
        else {
            App::setLocale(config('app.locale')); // همان APP_LOCALE از .env
        }

        // dd(App::getLocale());
        return $next($request);
    }
}
