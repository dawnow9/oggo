<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TaskStatus: string implements HasLabel, HasColor
{
    case TODO = "TODO";
    case IN_PROGRESS = "IN_PROGRESS";
    case FINISHED = "FINISHED";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TODO => __("tasks.status.todo"),
            self::IN_PROGRESS => __("tasks.status.inProgress"),
            self::FINISHED => __("tasks.status.finished"),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::TODO => "gray",
            self::IN_PROGRESS => "warning",
            self::FINISHED => "success",
        };
    }
}
