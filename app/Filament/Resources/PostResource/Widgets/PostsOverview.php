<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PostsOverview extends ChartWidget
{
    protected static ?string $heading = 'Post status';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $postsAggregateStatusCount = Post::query()->select('status', DB::raw('count(*) as total'))->groupBy('status')->get();
        $data = [];
        $labels = [];
        foreach ($postsAggregateStatusCount as $postCount) {
            $data[] = $postCount->total;
            $labels[] = $postCount->status;
        }
        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => ['#36A2EB', '#05ff8a', '#ffcd05', '#ff4305'],
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
