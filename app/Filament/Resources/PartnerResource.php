<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'B2B & Affiliates';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('B2B Corporate Identity')
                    ->description('External travel agency, corporate sponsor, or legal partner')
                    ->columns(3)
                    ->schema([
                        TextInput::make('partner_code')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated (e.g. B2B-2026-0001)'),

                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Agency / Company Name'),

                        TextInput::make('trade_license_number')
                            ->maxLength(100)
                            ->label('Commercial Registration / License #'),

                        TextInput::make('country')
                            ->maxLength(100)
                            ->default('Qatar'),

                        TextInput::make('city')
                            ->maxLength(100)
                            ->default('Doha'),
                    ]),

                Section::make('Point of Contact & Account Governance')
                    ->description('Designated operational contact and internal manager')
                    ->columns(3)
                    ->schema([
                        TextInput::make('contact_person')
                            ->required()
                            ->maxLength(255)
                            ->label('Authorized Contact Person'),

                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(50),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Select::make('account_manager_id')
                            ->label('VV Account Relationship Manager')
                            ->relationship('accountManager', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Commercial Contract & Credit Facilities')
                    ->description('Approved credit limits, payment terms, and agreement status')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending Verification',
                                'active' => 'Active Partner',
                                'suspended' => 'Suspended (Credit Hold)',
                                'terminated' => 'Terminated',
                            ])
                            ->default('active')
                            ->required(),

                        TextInput::make('credit_limit_qar')
                            ->numeric()
                            ->prefix('QAR')
                            ->default(0.00)
                            ->label('Approved Credit Facility (QAR)'),

                        TextInput::make('payment_terms_days')
                            ->numeric()
                            ->default(30)
                            ->suffix('Days')
                            ->label('Payment Net Terms'),

                        DatePicker::make('agreement_expiry_date')
                            ->label('SLA Contract Expiry Date'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partner_code')
                    ->label('Partner #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('company_name')
                    ->label('Company / Agency')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contact_person')
                    ->label('Key Contact')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Phone'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended', 'terminated' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('credit_limit_qar')
                    ->label('Credit Line')
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
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                    ]),
                TrashedFilter::make(),
            ])
            ->actions([
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
