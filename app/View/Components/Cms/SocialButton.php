<?php

namespace App\View\Components\Cms;

use Closure;
use App\Models\Post;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SocialButton extends Component
{

    public $socialButtons = [];

    /**
     * Create a new component instance.
     */
    public function __construct(Post $post)
    {
        foreach(config('cms.sharing.services') as $social => $settings)
        {
            if ($settings['follow_url']!='') {
                $href = $settings['follow_url'];
                $icon = $settings['icon'];
                $color = $settings['color'];
                $name = ucfirst($social);

                $this->socialButtons[] = [
                    'href' => $href,
                    'icon' => $icon,
                    'color' => $color,
                    'name' => $name
                ];
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cms.social-button');
    }
}
