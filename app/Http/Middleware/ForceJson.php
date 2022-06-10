<?php

namespace App\Http\Middleware;

use App\VersionHelper;
use Closure;
use Illuminate\Http\Request;

class ForceJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        $response = $next($request);
        $version = VersionHelper::version();
        $response->headers->set('maliin-version', $version);
        return $response;
    }

}
