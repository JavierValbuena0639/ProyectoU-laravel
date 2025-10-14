<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('app_locale');
        if ($locale) {
            AppFacade::setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}