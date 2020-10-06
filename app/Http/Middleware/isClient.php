<?php

namespace App\Http\Middleware;

use Closure;

class isClient
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
            if (auth()->user()->isClient()) {
                return $next($request);
            } else {
                abort(403, 'unAuthorized Request');
            }
        } else {
            abort(403, 'Unauthorized Request');
        }
    }
}
