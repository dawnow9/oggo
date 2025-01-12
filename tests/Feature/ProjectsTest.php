<?php

use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Models\Project;
use function Pest\Livewire\livewire;

it("can validate form", function () {
    livewire(CreateProject::class)
        ->fillForm([
            "name" => null,
            "description" => null,
            "start_at" => null,
        ])
        ->call("create")
        ->assertHasFormErrors([
            "name" => "required",
            "description" => "required",
            "start_at" => "required",
        ]);
});
