<?php

namespace App\Filament\Resources\VisaCaseResource\RelationManagers;

use App\Models\TimelineEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Auth;

class TimelineEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'timelineEvents';

    protected static ?string $title = 'Case Audit Timeline (Client-Safe & Internal Logs)';

    protected static ?string $icon = 'heroicon-o-clock';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('event_type')
                    ->options([
                        'status_changed'    => 'Status Changed',
                        'document_uploaded' => 'Document Uploaded',
                        'document_verified' => 'Document QC Verified',
                        'document_rejected' => 'Document QC Rejected',
                        'appointment_booked'=> 'Embassy/VAC Appointment Booked',
                        'payment_recorded'  => 'Payment Recorded',
                        'note_added'        => 'Case Note Added',
                        'officer_reassigned'=> 'Officer Reassigned',
                    ])
                    ->default('note_added')
                    ->required(),

                TextInput::make('title')
                    ->label('Event Title')
                    ->required()
                    ->maxLength(255),

                Toggle::make('is_client_visible')
                    ->label('Client Safe (Visible in Client Portal)')
                    ->helperText('Enable to display this event on the client portal timeline. Unchecked stays internal.')
                    ->default(true),

                Textarea::make('description')
                    ->label('Details / Remarks')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('event_type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('title')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),

                IconColumn::make('is_client_visible')
                    ->label('Client Visible')
                    ->boolean(),

                TextColumn::make('actor.name')
                    ->label('Logged By')
                    ->placeholder('System'),

                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['actor_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
