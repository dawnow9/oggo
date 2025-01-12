<?php

use App\Filament\Resources\TaskResource\Pages\CreateTask;
use function Pest\Livewire\livewire;

it("can validate form", function () {
    livewire(CreateTask::class)
        ->fillForm([
            "name" => null,
            "description" => null,
            "start_at" => null,
            "status" => null,
        ])
        ->call("create")
        ->assertHasFormErrors([
            "name" => "required",
            "description" => "required",
            "start_at" => "required",
            "status" => "required",
        ]);
});
