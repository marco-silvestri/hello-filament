<?php

namespace App\Listeners\Cms;

use Exception;
use App\Models\Post;
use App\Models\Visit;
use Illuminate\Support\Facades\Log;
use App\Events\Cms\LandingOnContent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackVisit implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LandingOnContent $event): void
    {

        try{
            $slug = $event->slug;

            $post = Post::where('slug',$slug)->first();
            $post->visits()->create([
                'user_id' => $event->userId,
            ]);

        }catch(Exception $e)
        {
            Log::error("Cannot track visit: $event" . PHP_EOL . "Because {$e->getMessage()}");
        }

    }
}
