<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use App\Models\Client;
use App\Models\User;
use App\Models\LeadSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'CRM & Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client Identity & Contact')
                    ->description('Primary contact info and direct messaging channels')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Full Name'),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50)
                            ->label('Phone Number'),

                        TextInput::make('whatsapp')
                            ->tel()
                            ->maxLength(50)
                            ->label('WhatsApp Number')
                            ->helperText('Used for direct WhatsApp integration'),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('nationality')
                            ->maxLength(100),

                        Select::make('qatar_residence_status')
                            ->label('Qatar Residence Status')
                            ->options([
                                'citizen' => 'Qatari Citizen',
                                'resident' => 'Qatar Resident (QID Holder)',
                                'visit_visa' => 'Tourist / Visit Visa',
                                'outside_qatar' => 'Applying from Outside Qatar',
                            ])
                            ->default('resident'),
                    ]),

                Section::make('Service Requirements')
                    ->description('Target destination and requested visa or business service')
                    ->columns(3)
                    ->schema([
                        TextInput::make('destination_country')
                            ->label('Destination Country')
                            ->placeholder('e.g. United Kingdom, Schengen, Canada'),

                        TextInput::make('visa_or_service_type')
                            ->label('Requested Visa / Service')
                            ->placeholder('e.g. Tourist Visa, Business Formation'),

                        DatePicker::make('planned_travel_date')
                            ->label('Intended Travel / Completion Date'),
                    ]),

                Section::make('Pipeline & Assignment')
                    ->description('Sales progression and ownership')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->required()
                            ->options([
                                'new' => 'New Lead',
                                'contacted' => 'Contacted',
                                'qualified' => 'Qualified',
                                'consultation' => 'Consultation Scheduled',
                                'quotation_sent' => 'Quotation Sent',
                                'follow_up' => 'Follow-up Required',
                                'converted' => 'Converted to Client',
                                'lost' => 'Lost / Unqualified',
                                'duplicate' => 'Duplicate Record',
                            ])
                            ->default('new'),

                        TextInput::make('lead_score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(50)
                            ->label('Lead Score (0-100)'),

                        Select::make('source_id')
                            ->label('Lead Source')
                            ->relationship('source', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('campaign')
                            ->placeholder('e.g. Google Ads Q4, Meta UK Promo')
                            ->maxLength(100),

                        Select::make('assigned_to')
                            ->label('Assigned Sales Officer')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Duplicate Detection & Compliance')
                    ->description('Verification to prevent multiple entries')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        TextInput::make('passport_no')
                            ->label('Passport Number')
                            ->maxLength(50),

                        TextInput::make('qid_no')
                            ->label('Qatar ID (QID) Number')
                            ->maxLength(50),

                        Toggle::make('is_duplicate')
                            ->label('Flagged as Duplicate')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lead_number')
                    ->label('Lead #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('Prospect Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->url(fn (Lead $record): ?string => $record->whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->whatsapp) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('destination_country')
                    ->label('Destination')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'contacted' => 'info',
                        'qualified' => 'primary',
                        'consultation' => 'warning',
                        'quotation_sent' => 'warning',
                        'converted' => 'success',
                        'lost', 'duplicate' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('lead_score')
                    ->label('Score')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 70 => 'success',
                        $state >= 40 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New Lead',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'consultation' => 'Consultation Scheduled',
                        'quotation_sent' => 'Quotation Sent',
                        'converted' => 'Converted',
                        'lost' => 'Lost',
                    ]),
                SelectFilter::make('source_id')
                    ->label('Lead Source')
                    ->relationship('source', 'name'),
                TrashedFilter::make(),
            ])
            ->actions([
                Action::make('convert_to_client')
                    ->label('Convert to Client')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('success')
                    ->modalHeading('Convert Lead to Master Client')
                    ->modalDescription('Verify applicant details to create an official Client 360 profile.')
                    ->modalSubmitActionLabel('Create Client Profile')
                    ->visible(fn (Lead $record): bool => $record->status !== 'converted')
                    ->form([
                        TextInput::make('full_name')
                            ->label('Full Legal Name')
                            ->required()
                            ->default(fn (Lead $record) => $record->name),
                        TextInput::make('phone')
                            ->label('Primary Phone')
                            ->tel()
                            ->required()
                            ->default(fn (Lead $record) => $record->phone ?: $record->whatsapp ?: '+974'),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp Number')
                            ->tel()
                            ->default(fn (Lead $record) => $record->whatsapp ?: $record->phone),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->default(fn (Lead $record) => $record->email),
                        TextInput::make('nationality')
                            ->label('Nationality')
                            ->required()
                            ->default(fn (Lead $record) => $record->nationality ?: 'Qatar'),
                        TextInput::make('qid_number')
                            ->label('Qatar ID (QID)')
                            ->default(fn (Lead $record) => $record->qid_no),
                        TextInput::make('passport_number')
                            ->label('Passport Number')
                            ->default(fn (Lead $record) => $record->passport_no),
                        Select::make('qatar_residence_status')
                            ->label('Qatar Residence Status')
                            ->options([
                                'citizen' => 'Qatari Citizen',
                                'resident' => 'Qatar Resident (QID Holder)',
                                'visit_visa' => 'Tourist / Visit Visa',
                                'outside_qatar' => 'Outside Qatar',
                            ])
                            ->default(fn (Lead $record) => $record->qatar_residence_status ?: 'resident'),
                    ])
                    ->action(function (Lead $record, array $data) {
                        $client = Client::create([
                            'full_name' => $data['full_name'],
                            'phone' => $data['phone'],
                            'whatsapp' => $data['whatsapp'] ?? null,
                            'email' => $data['email'] ?? null,
                            'nationality' => $data['nationality'],
                            'qid_number' => $data['qid_number'] ?? null,
                            'passport_number' => $data['passport_number'] ?? null,
                            'qatar_residence_status' => $data['qatar_residence_status'] ?? 'resident',
                        ]);

                        $record->update(['status' => 'converted']);

                        Notification::make()
                            ->title('Lead Converted Successfully')
                            ->body("Master Client {$client->client_code} ({$client->full_name}) created.")
                            ->success()
                            ->send();
                    }),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
