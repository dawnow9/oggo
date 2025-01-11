<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function (
                $record,
                Action $action
            ) {
                if ($record->tasks()->exists()) {
                    Notification::make()
                        ->danger()
                        ->title(__("project.action.delete.error.stillHasTasks"))
                        ->send();
                    $action->cancel();
                }
            }),
        ];
    }
}
