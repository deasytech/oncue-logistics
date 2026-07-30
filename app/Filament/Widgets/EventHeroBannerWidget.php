<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class EventHeroBannerWidget extends Widget
{
  protected static string $view = 'filament.widgets.event-hero-banner';

  public function getViewData(): array
  {
    $routeName = request()->route()->getName();

    // Customize content based on the current route
    switch ($routeName) {
      case 'filament.admin.resources.events.index':
        return [
          'title' => 'Events Management',
          'subtitle' => 'Organize and manage all your events in one place',
          'icon' => 'heroicon-o-calendar-days',
          'gradient' => 'from-pink-500 to-purple-600'
        ];
      case 'filament.admin.resources.events.create':
        return [
          'title' => 'Create New Event',
          'subtitle' => 'Add a new event to your collection',
          'icon' => 'heroicon-o-plus-circle',
          'gradient' => 'from-green-500 to-blue-600'
        ];
      case 'filament.admin.resources.events.edit':
        return [
          'title' => 'Edit Event',
          'subtitle' => 'Update your event details and settings',
          'icon' => 'heroicon-o-pencil-square',
          'gradient' => 'from-orange-500 to-red-600'
        ];
      default:
        return [
          'title' => 'Events Management',
          'subtitle' => 'Manage your events',
          'icon' => 'heroicon-o-calendar-days',
          'gradient' => 'from-pink-500 to-purple-600'
        ];
    }
  }
}
