<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisaCaseResource\Pages;
use App\Models\VisaCase;
use App\Models\Client;
use App\Models\Country;
use App\Models\Service;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class VisaCaseResource extends Resource
{
    protected static ?string $model = VisaCase::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Case Processing';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Case Identification & Service Assignment')
                    ->description('Client link and service configuration (1 Client -> Multiple Cases)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('case_number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated (e.g. CS-2026-0001)'),

                        Select::make('client_id')
                            ->label('Client Profile')
                            ->relationship('client', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('country_id')
                            ->label('Destination Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('service_id')
                            ->label('Service / Visa Track')
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('assigned_officer_id')
                            ->label('Assigned Visa Officer')
                            ->relationship('assignedOfficer', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('priority')
                            ->options([
                                'normal' => 'Normal Priority',
                                'urgent' => 'Urgent / Express',
                                'vip' => 'VIP Executive Handling',
                            ])
                            ->default('normal'),
                    ]),

                Section::make('Workflow Status & Milestones')
                    ->description('Operational milestones and processing deadlines')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->required()
                            ->options([
                                'inquiry' => 'Initial Inquiry',
                                'documents_pending' => 'Documents Pending',
                                'under_review' => 'Under Internal QC Review',
                                'appointment_booked' => 'VAC / Embassy Appointment Booked',
                                'submitted' => 'Application Submitted',
                                'decision_pending' => 'Embassy Decision Pending',
                                'approved' => 'Visa Approved / Issued',
                                'rejected' => 'Visa Refused / Rejected',
                                'appeal_in_progress' => 'Appeal / Reapplication In Progress',
                                'closed' => 'Case Closed / Delivered',
                            ])
                            ->default('documents_pending'),

                        DatePicker::make('intended_travel_date')
                            ->label('Planned Travel Date'),

                        DatePicker::make('submission_deadline')
                            ->label('Filing Deadline'),

                        DateTimePicker::make('sla_due_at')
                            ->label('SLA Resolution Target'),
                    ]),

                Section::make('Commercial & Payments (QAR)')
                    ->description('Financial summary and payment tracking')
                    ->columns(4)
                    ->schema([
                        TextInput::make('total_amount_qar')
                            ->numeric()
                            ->prefix('QAR')
                            ->label('Total Agreed Fee')
                            ->default(0.00)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $total = (float) $get('total_amount_qar');
                                $paid = (float) $get('paid_amount_qar');
                                $balance = max(0, $total - $paid);
                                $set('balance_qar', number_format($balance, 2, '.', ''));
                                if ($paid <= 0) {
                                    $set('payment_status', 'unpaid');
                                } elseif ($paid >= $total && $total > 0) {
                                    $set('payment_status', 'fully_paid');
                                } else {
                                    $set('payment_status', 'partially_paid');
                                }
                            }),

                        TextInput::make('paid_amount_qar')
                            ->numeric()
                            ->prefix('QAR')
                            ->label('Paid Amount')
                            ->default(0.00)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $total = (float) $get('total_amount_qar');
                                $paid = (float) $get('paid_amount_qar');
                                $balance = max(0, $total - $paid);
                                $set('balance_qar', number_format($balance, 2, '.', ''));
                                if ($paid <= 0) {
                                    $set('payment_status', 'unpaid');
                                } elseif ($paid >= $total && $total > 0) {
                                    $set('payment_status', 'fully_paid');
                                } else {
                                    $set('payment_status', 'partially_paid');
                                }
                            }),

                        TextInput::make('balance_qar')
                            ->numeric()
                            ->prefix('QAR')
                            ->label('Remaining Balance')
                            ->default(0.00)
                            ->readOnly(),

                        Select::make('payment_status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'partially_paid' => 'Partially Paid',
                                'fully_paid' => 'Fully Paid',
                                'refunded' => 'Refunded',
                            ])
                            ->default('unpaid'),
                    ]),

                Section::make('Internal vs. Client Communication (Strict Separation)')
                    ->description('Ensures internal notes are NEVER exposed on the client portal')
                    ->columns(2)
                    ->schema([
                        Textarea::make('client_notes')
                            ->label('Client-Visible Guidance')
                            ->placeholder('Instructions shown in the client portal (e.g. Please bring original passport to TLScontact on Wednesday).')
                            ->rows(4),

                        Textarea::make('internal_notes')
                            ->label('Staff Internal Notes (Confidential)')
                            ->placeholder('Internal officer discussion, document doubts, embassy intelligence. NEVER visible to clients.')
                            ->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('Case #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('client.full_name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.name')
                    ->label('Destination')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('service.name')
                    ->label('Service')
                    ->limit(25)
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'documents_pending', 'inquiry' => 'warning',
                        'under_review' => 'info',
                        'appointment_booked', 'submitted' => 'primary',
                        'approved', 'closed' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'vip' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('assignedOfficer.name')
                    ->label('Officer')
                    ->placeholder('Unassigned'),

                TextColumn::make('balance_qar')
                    ->label('Balance')
                    ->money('QAR')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'documents_pending' => 'Documents Pending',
                        'under_review' => 'Under Review',
                        'appointment_booked' => 'Appointment Booked',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('country_id')
                    ->label('Destination')
                    ->relationship('country', 'name'),
                SelectFilter::make('priority')
                    ->options([
                        'normal' => 'Normal',
                        'urgent' => 'Urgent',
                        'vip' => 'VIP',
                    ]),
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VisaCaseResource\RelationManagers\DocumentsRelationManager::class,
            VisaCaseResource\RelationManagers\TasksRelationManager::class,
            VisaCaseResource\RelationManagers\AppointmentsRelationManager::class,
            VisaCaseResource\RelationManagers\TimelineEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisaCases::route('/'),
            'create' => Pages\CreateVisaCase::route('/create'),
            'view' => Pages\ViewVisaCase::route('/{record}'),
            'edit' => Pages\EditVisaCase::route('/{record}/edit'),
        ];
    }
}
