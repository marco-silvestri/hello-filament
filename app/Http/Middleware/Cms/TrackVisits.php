<?php

namespace App\Http\Middleware\Cms;

use App\Events\Cms\LandingOnContent;
use Closure;
use Exception;
use App\Models\Post;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        event(new LandingOnContent($request['slug'], $userId));

        return $next($request);
    }
}
