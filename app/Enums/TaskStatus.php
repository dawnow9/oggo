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
            self::TODO => "To Do",
            self::IN_PROGRESS => "In Progress",
            self::FINISHED => "Finished",
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
