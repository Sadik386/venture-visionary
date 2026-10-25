<?php

namespace App\Filament\Resources\VisaCaseResource\RelationManagers;

use App\Models\Appointment;
use App\Models\TimelineEvent;
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
use Illuminate\Support\Facades\Auth;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    protected static ?string $title = 'Embassy & VAC Appointments (Biometrics / Interviews)';

    protected static ?string $icon = 'heroicon-o-calendar-days';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('appointment_type')
                    ->options([
                        'biometrics'      => 'Biometrics Enrolment',
                        'interview'       => 'Embassy Consular Interview',
                        'document_drop'   => 'Document Submission (VAC/TLS/VFS)',
                        'passport_pickup' => 'Passport Collection',
                        'consultation'    => 'Client Strategy Session',
                    ])
                    ->default('biometrics')
                    ->required(),

                TextInput::make('location_name')
                    ->label('Location / Center')
                    ->required()
                    ->placeholder('e.g. VFS Global Doha, German Embassy Doha, TLScontact')
                    ->maxLength(255),

                DateTimePicker::make('appointment_time')
                    ->label('Appointment Date & Time')
                    ->required(),

                Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled / Confirmed',
                        'completed' => 'Completed Successfully',
                        'cancelled' => 'Cancelled',
                        'no_show'   => 'No Show / Missed',
                    ])
                    ->default('scheduled')
                    ->required(),

                Textarea::make('notes')
                    ->label('Appointment Instructions / Requirements')
                    ->placeholder('e.g. Bring printed appointment letter, 2 passport photos, original bank statement.')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('appointment_type')
            ->columns([
                TextColumn::make('appointment_type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('location_name')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('appointment_time')
                    ->label('Date & Time')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'scheduled' => 'warning',
                        'cancelled', 'no_show' => 'danger',
                        default     => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('notes')
                    ->limit(40)
                    ->wrap(),
            ])
            ->defaultSort('appointment_time', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (Appointment $record) {
                        TimelineEvent::create([
                            'case_id' => $record->case_id,
                            'actor_id' => Auth::id(),
                            'event_type' => 'appointment_booked',
                            'title' => "Appointment Scheduled: " . ucfirst(str_replace('_', ' ', $record->appointment_type)),
                            'description' => "Location: {$record->location_name} on " . $record->appointment_time?->format('d M Y H:i'),
                            'is_client_visible' => true,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
