<?php

namespace App\View\Components\Widgets;

use Closure;
use App\Models\Post;
use Illuminate\View\Component;
use App\Traits\Cms\HasPostsCaching;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use CyrildeWit\EloquentViewable\Support\Period;

class MostRead extends Component
{
    use HasPostsCaching;

    public $mostRead;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->mostRead =
            Cache::flexible(
                'most-read-posts',
                $this->getFlexibleTtl(),
                function(){
                return Post::with('categories')
                    ->orderByViews('desc', Period::pastDays(5))
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
