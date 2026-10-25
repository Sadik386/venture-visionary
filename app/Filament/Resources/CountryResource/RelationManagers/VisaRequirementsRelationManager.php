<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use App\Models\VisaRequirement;
use App\Models\VisaType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class VisaRequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'visaRequirements';

    protected static ?string $title = 'Visa Master Rules & Governance (Maker-Checker)';

    protected static ?string $icon = 'heroicon-o-shield-check';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Visa Track & Fee Rules')
                    ->columns(3)
                    ->schema([
                        Select::make('visa_type_id')
                            ->label('Visa Type / Category')
                            ->relationship('visaType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('official_fee_qar')
                            ->label('Official Embassy Fee (QAR)')
                            ->numeric()
                            ->default(0.00)
                            ->prefix('QAR')
                            ->required(),

                        TextInput::make('vac_fee_qar')
                            ->label('VAC Center Fee (QAR)')
                            ->numeric()
                            ->default(0.00)
                            ->prefix('QAR'),

                        TextInput::make('estimated_processing_days')
                            ->label('Estimated Processing (Days)')
                            ->numeric()
                            ->default(15)
                            ->suffix('Days'),

                        Toggle::make('biometrics_required')
                            ->label('Biometrics Required at VAC')
                            ->default(true),

                        Toggle::make('interview_required')
                            ->label('Consular Interview Required')
                            ->default(false),
                    ]),

                Section::make('Maker-Checker Governance (Rule 1: Zero Hardcoding)')
                    ->description('Rule verification tracking and expiration alerts')
                    ->columns(3)
                    ->schema([
                        Select::make('verification_status')
                            ->label('Audit Verification Status')
                            ->options([
                                'verified'       => 'Verified (Maker-Checker Approved)',
                                'pending_review' => 'Pending Verification Review',
                                'unverified'     => 'Unverified / Draft Rule',
                            ])
                            ->default('verified')
                            ->required(),

                        DatePicker::make('last_verified_date')
                            ->label('Last Verified Date')
                            ->default(now()),

                        DatePicker::make('next_review_date')
                            ->label('Next Review Audit Date')
                            ->default(now()->addMonths(3)),
                    ]),

                Section::make('Dynamic Eligibility & Country Specifics')
                    ->columns(2)
                    ->schema([
                        KeyValue::make('qatar_resident_eligibility')
                            ->label('Qatar Resident Eligibility Criteria')
                            ->keyLabel('Requirement')
                            ->valueLabel('Standard Rule')
                            ->reorderable(),

                        KeyValue::make('nationality_specific_rules')
                            ->label('Nationality-Specific Exemptions & Restrictions')
                            ->keyLabel('Nationality Code / Group')
                            ->valueLabel('Condition / Exemption')
                            ->reorderable(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visaType.name')
            ->columns([
                TextColumn::make('visaType.name')
                    ->label('Visa Track')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('official_fee_qar')
                    ->label('Embassy Fee')
                    ->money('QAR')
                    ->sortable(),

                TextColumn::make('vac_fee_qar')
                    ->label('VAC Fee')
                    ->money('QAR')
                    ->sortable(),

                TextColumn::make('estimated_processing_days')
                    ->label('SLA Days')
                    ->suffix(' days'),

                IconColumn::make('biometrics_required')
                    ->label('Biometrics')
                    ->boolean(),

                TextColumn::make('verification_status')
                    ->label('Governance')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified'       => 'success',
                        'pending_review' => 'warning',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('last_verified_date')
                    ->label('Verified On')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
