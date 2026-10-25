<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Governance & Audit';

    protected static ?int $navigationSort = 10;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Audit Trail Details')
                    ->columns(3)
                    ->schema([
                        TextInput::make('action')
                            ->disabled(),

                        TextInput::make('user.name')
                            ->label('Actor / User')
                            ->disabled(),

                        TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),

                        TextInput::make('auditable_type')
                            ->label('Target Model')
                            ->disabled(),

                        TextInput::make('auditable_id')
                            ->label('Target Record ID')
                            ->disabled(),

                        TextInput::make('created_at')
                            ->label('Timestamp')
                            ->disabled(),

                        TextInput::make('user_agent')
                            ->label('User Agent')
                            ->columnSpanFull()
                            ->disabled(),

                        KeyValue::make('old_values')
                            ->label('Previous State (Before)')
                            ->columnSpan(1)
                            ->disabled(),

                        KeyValue::make('new_values')
                            ->label('Modified State (After)')
                            ->columnSpan(2)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Actor')
                    ->searchable()
                    ->placeholder('System / Guest'),

                TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'export', 'sensitive_data_export' => 'danger',
                        'delete', 'force_delete'         => 'danger',
                        'update', 'qc_rejected'          => 'warning',
                        'create', 'qc_approved'          => 'success',
                        default                          => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('auditable_type')
                    ->label('Entity')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'System')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->fontFamily('mono')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'create'                => 'Create',
                        'update'                => 'Update',
                        'delete'                => 'Delete',
                        'export'                => 'Export',
                        'sensitive_data_export' => 'Sensitive Data Export',
                        'qc_approved'           => 'QC Approved',
                        'qc_rejected'           => 'QC Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view'  => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
