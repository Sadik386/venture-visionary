<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'CRM & Sales';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Primary Identification & Bio')
                    ->description('Master client profile details')
                    ->columns(3)
                    ->schema([
                        TextInput::make('client_code')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated (e.g. CL-2026-0001)'),

                        TextInput::make('full_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Full Legal Name'),

                        TextInput::make('passport_name')
                            ->maxLength(255)
                            ->label('Name as in Passport'),

                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth'),

                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ]),

                        TextInput::make('nationality')
                            ->maxLength(100),
                    ]),

                Section::make('Official Documents (Qatar & Passport)')
                    ->description('Crucial for visa eligibility checks')
                    ->columns(4)
                    ->schema([
                        TextInput::make('passport_number')
                            ->maxLength(50)
                            ->label('Passport Number'),

                        DatePicker::make('passport_expiry_date')
                            ->label('Passport Expiry Date'),

                        TextInput::make('qid_number')
                            ->maxLength(50)
                            ->label('Qatar ID (QID)'),

                        DatePicker::make('qid_expiry_date')
                            ->label('QID Expiry Date'),

                        Select::make('qatar_residence_status')
                            ->options([
                                'citizen' => 'Qatari Citizen',
                                'resident' => 'Qatar Resident (QID)',
                                'visit_visa' => 'Tourist / Visit Visa',
                                'outside_qatar' => 'Outside Qatar',
                            ])
                            ->default('resident'),

                        TextInput::make('qatar_address')
                            ->columnSpan(3)
                            ->label('Qatar Residential / Office Address'),
                    ]),

                Section::make('Contact & Notification Channels')
                    ->description('Direct client communication endpoints')
                    ->columns(3)
                    ->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('whatsapp')
                            ->tel()
                            ->maxLength(50)
                            ->helperText('Primary channel for automated OTP & case status alerts'),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client_code')
                    ->label('Client ID')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('full_name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nationality')
                    ->searchable()
                    ->badge(),

                TextColumn::make('qid_number')
                    ->label('QID')
                    ->searchable()
                    ->placeholder('N/A'),

                TextColumn::make('passport_number')
                    ->label('Passport #')
                    ->searchable()
                    ->placeholder('N/A'),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->url(fn (Client $record): ?string => $record->whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->whatsapp) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('cases_count')
                    ->counts('cases')
                    ->label('Active Cases')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('qatar_residence_status')
                    ->options([
                        'citizen' => 'Qatari Citizen',
                        'resident' => 'Qatar Resident',
                        'visit_visa' => 'Visit Visa',
                        'outside_qatar' => 'Outside Qatar',
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
