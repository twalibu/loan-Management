<?php

namespace App\Http\Middleware;

use Alert;
use Closure;
use Sentinel;

class TenantUserHasAccess
{
    use \Centaur\Middleware\TranslationHelper;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!Sentinel::check()) {
            return $this->denied($request);
        }

        if (!Sentinel::hasAccess($permission)) {
            return $this->denied($request);
        }

        return $next($request);
    }

    public function denied($request)
    {
        if ($request->ajax()) {
            $message = $this->translate('unauthorized', 'Unauthorized');
            return response()->json(['error' => $message], 401);
        } else {
            $message = $this->translate('need_permission', 'You do not have permission to do that.');
            Alert::error($message, 'Access Denied!');
            return redirect()->back();
        }
    }
}
