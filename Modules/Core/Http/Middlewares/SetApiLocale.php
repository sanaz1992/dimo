<?php

namespace Modules\Core\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // App Facade رو اضافه کن

class SetApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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
