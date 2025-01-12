<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class ProjectTasksCountChart extends ChartWidget
{
    protected static ?string $heading = "Tasks in project";

    protected function getData(): array
    {
        $projectsTasks = Task::query()
            ->join("projects", "tasks.project_id", "=", "projects.id")
            ->selectRaw(
                "projects.name as project_name, count(tasks.id) as tasks_count"
            )
            ->groupBy("projects.name")
            ->orderBy("projects.name")
            ->get();

        return [
            "datasets" => [
                [
                    "label" => "Tasks",
                    "data" => $projectsTasks->pluck("tasks_count"),
                ],
            ],
            "labels" => $projectsTasks->pluck("project_name"),
        ];
    }

    protected function getType(): string
    {
        return "bar";
    }
}
