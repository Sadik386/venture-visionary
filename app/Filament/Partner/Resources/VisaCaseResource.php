<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\VisaCaseResource\Pages;
use App\Models\VisaCase;
use App\Models\Client;
use App\Models\Country;
use App\Models\Service;
use App\Models\PartnerUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class VisaCaseResource extends Resource
{
    protected static ?string $model = VisaCase::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $modelLabel = 'Agency Visa Application';

    protected static ?string $pluralModelLabel = 'Agency Applications';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        if (! $user) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        $partnerUser = PartnerUser::where('email', $user->email)->first();
        if (! $partnerUser) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()->where('partner_id', $partnerUser->partner_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Agency Case Details')
                    ->description('Submit and track visa files processed under your B2B corporate account')
                    ->columns(3)
                    ->schema([
                        TextInput::make('case_number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated upon submission'),

                        Select::make('client_id')
                            ->label('Client / Applicant Profile')
                            ->relationship('client', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('full_name')
                                    ->label('Applicant Full Legal Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('nationality')
                                    ->label('Nationality')
                                    ->required()
                                    ->default('Qatar'),
                                TextInput::make('passport_number')
                                    ->label('Passport Number')
                                    ->maxLength(50),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ]),

                        Select::make('country_id')
                            ->label('Destination Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('service_id')
                            ->label('Service Track')
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('intended_travel_date')
                            ->label('Intended Travel Date'),

                        DatePicker::make('submission_deadline')
                            ->label('Filing Deadline'),
                    ]),

                Section::make('Applicant Guidance & Notes')
                    ->description('Notes and travel instructions visible to applicant')
                    ->schema([
                        Textarea::make('client_notes')
                            ->label('Application Notes / Client Instructions')
                            ->rows(3)
                            ->placeholder('e.g. Applicant will provide biometric appointment in Doha directly.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('Case Ref #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('client.full_name')
                    ->label('Applicant Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.name')
                    ->label('Destination')
                    ->badge()
                    ->color('info'),

                TextColumn::make('service.name')
                    ->label('Service'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'documents_pending', 'inquiry' => 'warning',
                        'under_review' => 'info',
                        'appointment_booked', 'submitted' => 'primary',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('total_amount_qar')
                    ->label('Net Contract Rate')
                    ->money('QAR')
                    ->sortable(),

                TextColumn::make('balance_qar')
                    ->label('Balance Due')
                    ->money('QAR'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            VisaCaseResource\RelationManagers\PartnerDocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisaCases::route('/'),
            'create' => Pages\CreateVisaCase::route('/create'),
            'view' => Pages\ViewVisaCase::route('/{record}'),
        ];
    }
}
