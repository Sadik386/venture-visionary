<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Lead;
use App\Models\User;
use App\Models\VisaCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Operations & Delivery';

    protected static ?string $navigationLabel = 'Appointment Calendar';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Schedule & Booking Specs')
                    ->description('Date, time, location, and meeting link')
                    ->columns(3)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. UK Visa Strategy Consultation')
                            ->columnSpan(2),

                        Select::make('appointment_type')
                            ->required()
                            ->options([
                                'consultation' => 'Initial / Visa Consultation',
                                'biometrics' => 'Biometrics (VFS / TLS / Embassy)',
                                'document_submission' => 'Document Verification & Submission',
                                'interview' => 'Embassy Interview Preparation',
                                'medical' => 'Medical Examination',
                            ])
                            ->default('consultation'),

                        DateTimePicker::make('appointment_time')
                            ->required()
                            ->label('Appointment Time (Qatar Time)')
                            ->native(false),

                        Select::make('status')
                            ->required()
                            ->options([
                                'scheduled' => 'Scheduled',
                                'attended' => 'Attended / Completed',
                                'missed' => 'Missed / No-Show',
                                'rescheduled' => 'Rescheduled',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('scheduled'),

                        TextInput::make('location_name')
                            ->required()
                            ->default('Venture Visionary Advisory Office, Doha')
                            ->label('Location / Venue'),

                        TextInput::make('meeting_link')
                            ->url()
                            ->placeholder('https://meet.google.com/xyz')
                            ->label('Virtual Meeting Link (Optional)'),
                    ]),

                Section::make('Participant & Consultant')
                    ->description('Associated prospect or case profile and host consultant')
                    ->columns(3)
                    ->schema([
                        Select::make('lead_id')
                            ->label('Link Lead Inbound')
                            ->relationship('lead', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $lead = Lead::find($state);
                                    if ($lead) {
                                        $set('client_name', $lead->name);
                                        $set('client_phone', $lead->phone ?? $lead->whatsapp);
                                        $set('client_email', $lead->email);
                                    }
                                }
                            }),

                        Select::make('case_id')
                            ->label('Link Visa Case File')
                            ->relationship('visaCase', 'case_number')
                            ->searchable()
                            ->preload(),

                        Select::make('assigned_to_id')
                            ->label('Assigned Host / Consultant')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),

                        TextInput::make('client_name')
                            ->required()
                            ->label('Applicant / Client Name'),

                        TextInput::make('client_phone')
                            ->tel()
                            ->label('Contact Phone / WhatsApp'),

                        TextInput::make('client_email')
                            ->email()
                            ->label('Email Address'),
                    ]),

                Section::make('Consultant Agenda & Meeting Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Special requirements, document checklist items, or discussion points...'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('appointment_time')
                    ->label('Date & Time')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('title')
                    ->label('Topic / Booking')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('client_name')
                    ->label('Applicant')
                    ->searchable()
                    ->description(fn (Appointment $record): string => $record->client_phone ?? ''),

                TextColumn::make('appointment_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'consultation' => 'info',
                        'biometrics' => 'warning',
                        'document_submission' => 'primary',
                        'interview' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('assignedTo.name')
                    ->label('Host Consultant')
                    ->searchable()
                    ->default('Advisory Team'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'attended' => 'success',
                        'missed' => 'danger',
                        'cancelled' => 'secondary',
                        default => 'warning',
                    }),
            ])
            ->defaultSort('appointment_time', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'attended' => 'Attended',
                        'missed' => 'Missed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('appointment_type')
                    ->options([
                        'consultation' => 'Consultation',
                        'biometrics' => 'Biometrics',
                        'document_submission' => 'Document Submission',
                        'interview' => 'Interview',
                    ]),
            ])
            ->actions([
                Action::make('mark_attended')
                    ->label('Attended')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Appointment $record): bool => $record->status === 'scheduled')
                    ->requiresConfirmation()
                    ->action(function (Appointment $record) {
                        $record->update(['status' => 'attended']);
                        Notification::make()
                            ->title('Appointment Marked Attended')
                            ->body('Status updated and post-appointment workflows triggered.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
