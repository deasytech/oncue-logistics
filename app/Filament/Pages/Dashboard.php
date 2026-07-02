<?php

namespace App\Filament\Pages;

use App\Filament\Resources\EventResource\Widgets\EventStats;
use App\Filament\Resources\ProjectInfoResource\Widgets\projectInfo;
use App\Filament\Widgets\CustomerAnalyticsWidget;
use App\Filament\Widgets\CustomerStatsWidget;
use App\Filament\Widgets\FabricRevenueWidget;
use App\Filament\Widgets\PaymentAnalyticsWidget;
use App\Filament\Widgets\PaymentStatsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RsvpStatusWidget;
use App\Filament\Widgets\UpcomingEventsWidget;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\MaxWidth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -1;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Dashboard';
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_customers')
                ->label('Customers')
                ->icon('heroicon-o-user-group')
                ->url(url('/admin/customers'))
                ->color('gray')
                ->size('sm'),
            Action::make('view_events')
                ->label('Events')
                ->icon('heroicon-o-calendar-days')
                ->url(url('/admin/events'))
                ->color('gray')
                ->size('sm'),
            Action::make('create_event')
                ->label('New Event')
                ->icon('heroicon-o-plus-circle')
                ->url(url('/admin/events/create'))
                ->color('primary')
                ->size('sm'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EventStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | string | array
    {
        return [
            'default' => 2,
            'md' => 4,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            projectInfo::class,
            CustomerStatsWidget::class,
            CustomerAnalyticsWidget::class,
            PaymentStatsWidget::class,
            FabricRevenueWidget::class,
            PaymentAnalyticsWidget::class,
            RsvpStatusWidget::class,
            UpcomingEventsWidget::class,
            RecentActivityWidget::class,
        ];
    }
}
