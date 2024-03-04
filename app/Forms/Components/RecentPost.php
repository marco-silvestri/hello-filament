<?php

namespace App\Forms\Components;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Field;

class RecentPost extends Field
{
    protected string $view = 'filament.forms.components.recent-post';

    public $selectedPost = 'pippo';

    public function getRecentPosts():?Collection
    {
        return Post::query()
            ->where('created_at', '>', Carbon::now()
                ->subMonth()
                ->format('Y-m-d H:i:s'))
            ->get();
    }

    public function changeEvent($id)
    {
        $this->selectedPost = $id;
    }

    public function getSelectedPost()
    {
        return $this->selectedPost;
    }
}
