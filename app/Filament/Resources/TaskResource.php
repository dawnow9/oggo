<?php

namespace App\Filament\Resources;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = "heroicon-o-rectangle-stack";

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make("name")->required(),
            DatePicker::make("start_at")->required(),
            DatePicker::make("end_at")->after("start_at"),
            Select::make("status")
                ->options(TaskStatus::class)
                ->default(TaskStatus::TODO)
                ->required(),
            Select::make("user_id")->relationship("user", "name"),
            Select::make("project_id")
                ->relationship("project", "name")
                ->required()
                ->hiddenOn("edit"),
            MarkdownEditor::make("description")->required()->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->searchable()->sortable(),
                TextColumn::make("start_at")->searchable()->sortable()->date(),
                TextColumn::make("end_at")->searchable()->sortable()->date(),
                TextColumn::make("status")->searchable()->sortable()->badge(),
                TextColumn::make("project.name")->searchable()->sortable(),
                TextColumn::make("user.name")->searchable()->sortable(),
            ])
            ->filters([
                Filter::make("name")
                    ->form([TextInput::make("name")])
                    ->query(
                        fn(
                            Builder $query,
                            array $data
                        ): Builder => $query->when(
                            $data["name"],
                            fn(
                                Builder $query,
                                $date
                            ): Builder => $query->whereLike("name", $date)
                        )
                    ),
                SelectFilter::make("status")->options(TaskStatus::class),
                SelectFilter::make("project_id")
                    ->label(__("tasks.filters.projectName"))
                    ->relationship("project", "name"),
                Filter::make("start_at")
                    ->form([
                        DatePicker::make("start_at_from")->label(
                            "Started after"
                        ),
                        DatePicker::make("start_at_to")->label(
                            "Started before"
                        ),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data["start_at_from"],
                                fn(
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    "start_at",
                                    ">=",
                                    $date
                                )
                            )
                            ->when(
                                $data["start_at_to"],
                                fn(
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    "start_at",
                                    "<=",
                                    $date
                                )
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data["start_at_from"] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(
                                "Started after " .
                                    Carbon::parse(
                                        $data["start_at_from"]
                                    )->toFormattedDateString()
                            )->removeField("start_at_from");
                        }

                        if ($data["start_at_to"] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(
                                "Started before " .
                                    Carbon::parse(
                                        $data["start_at_to"]
                                    )->toFormattedDateString()
                            )->removeField("start_at_to");
                        }

                        return $indicators;
                    }),
            ])
            ->deselectAllRecordsWhenFiltered()
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListTasks::route("/"),
            "create" => Pages\CreateTask::route("/create"),
            "edit" => Pages\EditTask::route("/{record}/edit"),
        ];
    }
}
