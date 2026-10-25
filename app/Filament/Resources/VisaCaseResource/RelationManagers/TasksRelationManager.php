<?php

namespace App\Filament\Resources\VisaCaseResource\RelationManagers;

use App\Models\Task;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Operational Case Tasks & SLAs';

    protected static ?string $icon = 'heroicon-o-clipboard-document-check';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('assigned_to_id')
                    ->label('Assigned Officer')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('priority')
                    ->options([
                        'low'      => 'Low Priority',
                        'normal'   => 'Normal Priority',
                        'high'     => 'High Priority',
                        'urgent'   => 'Urgent / Express Escalation',
                    ])
                    ->default('normal')
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'cancelled'   => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),

                DateTimePicker::make('due_at')
                    ->label('SLA / Due Deadline')
                    ->default(now()->addDays(2)),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'normal' => 'info',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'info',
                        'pending'     => 'warning',
                        'cancelled'   => 'gray',
                        default       => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('due_at')
                    ->label('Due Date')
                    ->dateTime('d M Y, H:i')
                    ->color(fn (Task $record) => $record->due_at && $record->due_at->isPast() && $record->status !== 'completed' ? 'danger' : null),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Action::make('complete')
                    ->label('Done')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Task $record) => $record->status !== 'completed')
                    ->action(fn (Task $record) => $record->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
