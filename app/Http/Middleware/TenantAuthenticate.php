<?php

namespace App\Http\Middleware;

use Alert;
use Closure;
use Sentinel;

class TenantAuthenticate
{
    use \Centaur\Middleware\TranslationHelper;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Sentinel::check()) {
            if ($request->ajax()) {
                $message = $this->translate('unauthorized', 'Unauthorized');
                return response()->json(['error' => $message], 401);
            } else {
                return redirect()->guest(route('auth.login.form'));
            }
        }

        return $next($request);
    }
}
