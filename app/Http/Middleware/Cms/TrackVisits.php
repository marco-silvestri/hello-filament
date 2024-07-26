<?php

namespace App\Http\Middleware\Cms;

use Closure;
use Illuminate\Http\Request;
use App\Events\Cms\LandingOnContent;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->user() ? auth()->user()->id : null;

        if(isset($request['slug']))
        {
            event(new LandingOnContent($request['slug'], $userId));
        }

        return $next($request);
    }
}
