<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ProjectResource\Widgets\ProjectTasksCountChart;
use App\Filament\Resources\TaskResource\Widgets\TaskStatusChart;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected function getHeaderWidgets(): array
    {
        return [ProjectTasksCountChart::make(), TaskStatusChart::make()];
    }
}
