<?php

namespace App\Filament\Resources\WorkflowResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Execution Audit Logs';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Executed At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'skipped' => 'gray',
                        'failed' => 'danger',
                        default => 'secondary',
                    }),

                TextColumn::make('trigger_event')
                    ->label('Trigger')
                    ->badge()
                    ->color('info'),

                TextColumn::make('entity_type')
                    ->label('Target Entity')
                    ->formatStateUsing(fn ($state, $record) => class_basename($state) . ' #' . substr($record->entity_id ?? '', 0, 8)),

                TextColumn::make('details')
                    ->label('Outcome Details')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                        }
                        return (string) $state;
                    })
                    ->wrap()
                    ->limit(100),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
