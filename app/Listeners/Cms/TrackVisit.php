<?php

namespace App\Listeners\Cms;

use Exception;
use App\Models\Post;
use App\Models\Slug;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\Cms\LandingOnContent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;

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
        try {
            $slug = $event->slug;

            if (request()->routeIs('category') || request()->routeIs('tag')) {
                $visitsId = $this->insertVisits($event);
                $sluggable = $this->getSlugName($slug);
                $this->insertVisitables($visitsId, $sluggable);
            } else {
                $post = Post::whereHas('slug', function (Builder $query)  use ($slug) {
                    $query->where('name', $slug);
                })->first();
                if ($post) {
                    $now = now();
                    $post->visits()->create([
                        'user_id' => $event->userId,
                        'created_at' => $now,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error("Cannot track visit: $event->slug" . PHP_EOL . "Because {$e->getMessage()}");
        }
    }

    private function insertVisits($event): int
    {
        $visit = Visit::create([
            'user_id' => $event->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $visit->id;
    }

    private function getSlugName($slug): Slug
    {
        $slug = basename($slug);
        return Slug::where('name', $slug)->first();
    }

    private function insertVisitables($visitsId, $sluggable): void
    {
        DB::table('visitables')->insert([
            'visit_id' => $visitsId,
            'visitable_type' => $sluggable->sluggable_type,
            'visitable_id' => $sluggable->sluggable_id,
        ]);
    }
}
