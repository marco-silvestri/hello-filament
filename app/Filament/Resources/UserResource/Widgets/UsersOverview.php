<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UsersOverview extends ChartWidget
{
    protected static ?string $heading = 'New Users';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $start = now()->startOfYear();
        $end = now()->endOfYear();
        
        if ($activeFilter == 'year') {
            $start = now()->startOfYear();
            $end = now()->endOfYear();
        } elseif ($activeFilter == 'lastYear') {
            $start = now()->subYear()->startOfYear();
            $end = now()->subYear()->endOfYear();
        }
        $data = Trend::model(User::class)
            ->between(
                start: $start,
                end: $end,
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New users',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'year' => 'This year',
            'lastYear' => 'Last year'
        ];
    }
}
