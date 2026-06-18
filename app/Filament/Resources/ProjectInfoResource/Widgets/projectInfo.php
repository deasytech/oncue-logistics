<?php

namespace App\Filament\Resources\ProjectInfoResource\Widgets;

use Filament\Widgets\Widget;

class projectInfo extends Widget
{
    protected static string $view = 'filament.resources.project-info-resource.widgets.project-info';

    protected int | string | array $columnSpan = 'full';
}
