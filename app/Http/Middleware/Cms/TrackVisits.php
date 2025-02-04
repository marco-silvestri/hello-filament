<?php

namespace App\Http\Middleware\Cms;

use Closure;
use App\Models\Post;
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
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $slug = $request['slug'];
        if(isset($request['slug']))
        {
            $post = Post::whereHas('slug', function ($query) use ($slug) {
                $query->where('name', $slug);
            })->first();

            if($post)
            {
                views($post)
                    ->cooldown(1)
                    ->record();
            }
        }
    }
}
