<?php

namespace App\Filament\Pages;

use App\Filament\Resources\EventResource\Widgets\EventStats;
use App\Filament\Resources\ProjectInfoResource\Widgets\projectInfo;
use App\Filament\Widgets\CustomerAnalyticsWidget;
use App\Filament\Widgets\PaymentAnalyticsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RsvpStatusWidget;
use App\Filament\Widgets\UpcomingEventsWidget;
use Filament\Pages\Page;

class Dashboard extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-home';

  protected static string $view = 'filament-panels::pages.dashboard';

  protected static ?string $navigationGroup = 'Analytics';

  protected static ?int $navigationSort = 0;

  protected function getHeaderWidgets(): array
  {
    return [
      EventStats::class,
    ];
  }

  public function getHeaderWidgetsColumns(): int | string | array
  {
    return 4;
  }

  public function getColumns(): int | string | array
  {
    return 12;
  }

  public function getVisibleWidgets(): array
  {
    return $this->getWidgets();
  }

  protected function getWidgets(): array
  {
    return [
      projectInfo::class,
      CustomerAnalyticsWidget::class,
      PaymentAnalyticsWidget::class,
      RsvpStatusWidget::class,
      UpcomingEventsWidget::class,
      RecentActivityWidget::class,
    ];
  }

  protected function getWidgetsColumns(): int | array
  {
    return 12;
  }
}
