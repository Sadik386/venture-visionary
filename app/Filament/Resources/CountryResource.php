<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Master Database';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Country Master Configuration')
                    ->description('Universal master record supporting 193+ destinations without hard-coded code')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Country Name'),

                        TextInput::make('iso2')
                            ->required()
                            ->maxLength(2)
                            ->label('ISO2 Code (e.g. GB)'),

                        TextInput::make('iso3')
                            ->required()
                            ->maxLength(3)
                            ->label('ISO3 Code (e.g. GBR)'),

                        TextInput::make('currency_code')
                            ->default('USD')
                            ->maxLength(3)
                            ->label('Default Currency'),

                        TextInput::make('official_vac_name')
                            ->label('Official VAC Partner')
                            ->placeholder('e.g. VFS Global, TLScontact, BLS'),

                        TextInput::make('official_portal_url')
                            ->url()
                            ->label('Embassy / Visa Portal URL')
                            ->placeholder('https://visa.embassy.gov'),

                        TextInput::make('embassy_in_qatar_address')
                            ->columnSpan(2)
                            ->label('Embassy Location in Doha, Qatar'),

                        Toggle::make('is_active')
                            ->label('Active for Client Inquiries')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Country')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('iso2')
                    ->label('ISO-2')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('iso3')
                    ->label('ISO-3')
                    ->badge()
                    ->color('info'),

                TextColumn::make('currency_code')
                    ->label('Currency'),

                TextColumn::make('official_vac_name')
                    ->label('VAC / Embassy')
                    ->placeholder('Direct / Online'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('name', 'asc')
            ->filters([])
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
            CountryResource\RelationManagers\VisaRequirementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
