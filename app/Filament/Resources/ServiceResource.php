<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Master Database';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Service Definition')
                    ->description('Universal catalog defining visas, corporate structuring, and bank consultancy')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Service Name'),

                        TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->label('Unique Code')
                            ->placeholder('e.g. UK-TOURIST-STD'),

                        Select::make('country_id')
                            ->label('Associated Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        TextInput::make('standard_sla_days')
                            ->numeric()
                            ->default(15)
                            ->label('Standard Processing Days (SLA)'),

                        Select::make('currency')
                            ->options([
                                'QAR' => 'QAR (Qatari Riyal)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                                'GBP' => 'GBP (British Pound)',
                            ])
                            ->default('QAR')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Available for Quotations')
                            ->default(true),

                        Textarea::make('description')
                            ->columnSpan(3)
                            ->rows(2),
                    ]),

                Section::make('Default Price Schedule (Base Snapshot Values)')
                    ->description('Automatically captured when creating new cases or client quotations')
                    ->columns(3)
                    ->schema([
                        TextInput::make('default_government_fee')
                            ->numeric()
                            ->label('Government Visa Fee')
                            ->default(0.00),

                        TextInput::make('default_vac_fee')
                            ->numeric()
                            ->label('VAC / Biometric Center Fee')
                            ->default(0.00),

                        TextInput::make('default_consultancy_fee')
                            ->numeric()
                            ->label('Venture Visionary Agency Fee')
                            ->default(0.00)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->searchable()
                    ->placeholder('Universal / General'),

                TextColumn::make('default_consultancy_fee')
                    ->label('Agency Fee')
                    ->money(fn (Service $record) => $record->currency)
                    ->sortable(),

                TextColumn::make('standard_sla_days')
                    ->label('SLA')
                    ->suffix(' days'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('country_id')
                    ->label('Filter by Country')
                    ->relationship('country', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ServiceResource\RelationManagers\ServiceRulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
