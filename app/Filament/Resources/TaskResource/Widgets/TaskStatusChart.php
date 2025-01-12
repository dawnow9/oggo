<?php

namespace App\Filament\Resources\TaskResource\Widgets;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TaskStatusChart extends ChartWidget
{
    protected static ?string $heading = "Tasks by status";

    protected function getData(): array
    {
        if (Project::all()->count() === 0) {
            return ["datasets" => [], "labels" => []];
        }

        $activeFilters = $this->filter;

        $selectedProjectId = $activeFilters;

        if (!$selectedProjectId) {
            $selectedProjectId = Project::query()->orderBy("id")->first()->id;
        }

        $todoTasksCount = Task::query()
            ->where("project_id", $selectedProjectId)
            ->where("status", TaskStatus::TODO->value)
            ->count();

        $inProgressTasksCount = Task::query()
            ->where("project_id", $selectedProjectId)
            ->where("status", TaskStatus::IN_PROGRESS->value)
            ->count();

        $finishedTasksCount = Task::query()
            ->where("project_id", $selectedProjectId)
            ->where("status", TaskStatus::FINISHED->value)
            ->count();

        return [
            "datasets" => [
                [
                    "label" => "Tasks in status",
                    "data" => [
                        $todoTasksCount,
                        $inProgressTasksCount,
                        $finishedTasksCount,
                    ],
                ],
            ],
            "labels" => [
                TaskStatus::TODO->getLabel(),
                TaskStatus::IN_PROGRESS->getLabel(),
                TaskStatus::FINISHED->getLabel(),
            ],
        ];
    }

    protected function getType(): string
    {
        return "bar";
    }

    protected function getFilters(): ?array
    {
        $projects = Project::query()->orderBy("id")->get();

        return $projects
            ->mapWithKeys(
                fn(Project $project) => [$project->id => $project->name]
            )
            ->toArray();
    }
}
