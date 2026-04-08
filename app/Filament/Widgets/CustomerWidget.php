<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CustomerWidget extends ChartWidget
{
    protected ?string $heading = 'Customer Chart';
    protected static ? int $sort =2;

    protected function getData(): array
    {
            $data = Trend::model(Customer::class)
            ->between(
                start: now()->subMonth(),
                end: now(),
            )
            ->perMonth()
            ->count();


        return [
            'datasets' => [
                [
                    'label' => 'Customers',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => date('M Y', strtotime($value->date))),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
