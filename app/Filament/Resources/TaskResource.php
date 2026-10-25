<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\VisaCase;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Case Processing';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Task Assignment')
                    ->description('Assign operational tasks to staff members')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('case_id')
                            ->label('Linked Visa Case')
                            ->relationship('visaCase', 'case_number')
                            ->searchable()
                            ->preload(),

                        Select::make('assigned_to_id')
                            ->label('Assigned To')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('priority')
                            ->options([
                                'low'    => 'Low Priority',
                                'medium' => 'Medium Priority',
                                'high'   => 'High Priority',
                                'urgent' => 'Urgent – Do Immediately',
                            ])
                            ->default('medium')
                            ->required(),

                        Select::make('status')
                            ->options([
                                'pending'     => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed'   => 'Completed',
                                'overdue'     => 'Overdue',
                                'cancelled'   => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),

                        DateTimePicker::make('due_at')
                            ->label('Due Date & Time')
                            ->required(),

                        DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->nullable(),

                        Textarea::make('description')
                            ->label('Task Description / Instructions')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('semibold'),

                TextColumn::make('visaCase.case_number')
                    ->label('Case')
                    ->badge()
                    ->color('primary')
                    ->placeholder('General Task'),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'info',
                        'overdue'     => 'danger',
                        'cancelled'   => 'secondary',
                        default       => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->color(fn (Task $record) => $record->due_at->isPast() && $record->status !== 'completed' ? 'danger' : null),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('due_at', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'overdue'     => 'Overdue',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'urgent' => 'Urgent',
                        'high'   => 'High',
                        'medium' => 'Medium',
                        'low'    => 'Low',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (Task $record) => $record->status === 'completed')
                    ->action(function (Task $record) {
                        $record->update([
                            'status'       => 'completed',
                            'completed_at' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit'   => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
