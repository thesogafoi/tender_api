<?php

namespace App\Http\Middleware;

use Closure;

class isSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            if (auth()->user()->isSuperAdmin()) {
                return $next($request);
            } else {
                abort(403, 'unAuthorized Request');
            }
        } else {
            abort(403, 'Unauthorized Request');
        }
    }
}
