<?php

namespace App\Http\Middleware;

use App\Models\ClientPreference;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && $client = auth()->user()->getCurrentClient()) {
            $sessionKey = 'locale_client_'.$client->id;

            if (! session()->has($sessionKey)) {
                $locale = ClientPreference::getLanguage($client->id, 'es');
                session([$sessionKey => $locale]);
            }

            App::setLocale(session($sessionKey));
        } else {
            App::setLocale(config('app.locale', 'es'));
        }

        return $next($request);
    }
}
