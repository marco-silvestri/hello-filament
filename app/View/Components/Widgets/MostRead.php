<?php

namespace App\View\Components\Widgets;

use Closure;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\Visit;
use Illuminate\View\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class MostRead extends Component
{
    public $mostRead;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->mostRead =
            Cache::remember(
                'most-read-posts',
                600,
                function(){
                return Post::withCount(['visits' => function ($query) {
                    $query->where('created_at', '>', Carbon::now()->subDays(5));
                }])
                    ->orderBy('visits_count', 'desc')
                    ->with('categories')
                    ->limit(6)
                    ->get();
                }
            );
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.widgets.most-read');
    }
}
