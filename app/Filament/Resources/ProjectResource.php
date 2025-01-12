<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\Widgets\ProjectsCountChart;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = "heroicon-o-folder";

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make("name")->required()->maxLength(255),
            DatePicker::make("start_at")->required(),
            DatePicker::make("end_at")->after("start_at"),
            MarkdownEditor::make("description")->required()->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->searchable()->sortable(),

                TextColumn::make("start_at")->searchable()->sortable(),
                TextColumn::make("end_at")->searchable()->sortable(),
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
            "index" => Pages\ListProjects::route("/"),
            "create" => Pages\CreateProject::route("/create"),
            "edit" => Pages\EditProject::route("/{record}/edit"),
        ];
    }
}
