<?php

namespace App\View\Components\Cms;

use Closure;
use App\Models\Post;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SocialSharing extends Component
{

    public $shareButtons = [];

    /**
     * Create a new component instance.
     */
    public function __construct(Post $post)
    {
        foreach(config('cms.sharing.services') as $social => $settings)
        {
            $shareText = $settings['text'] ? '&text=' . urlencode($post->title) : '';
            $href = $settings['uri'] . $post->encoded_url . $shareText;
            $icon = $settings['icon'];
            $color = $settings['color'];
            $name = ucfirst($social);

            $this->shareButtons[] = [
                'href' => $href,
                'icon' => $icon,
                'color' => $color,
                'name' => $name,
            ];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cms.social-sharing');
    }
}
