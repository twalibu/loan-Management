<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class TenantGuest
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
        if (Sentinel::check()) {
            if ($request->ajax()) {
                $message = $this->translate('unauthorized', 'Unauthorized');
                return response()->json(['error' => $message], 401);
            } else {
                return redirect('dashboard');
            }
        }

        return $next($request);
    }
}
