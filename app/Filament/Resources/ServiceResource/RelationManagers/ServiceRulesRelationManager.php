<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Models\ServiceRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ServiceRulesRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    protected static ?string $title = 'Dynamic Checklist & Eligibility Rule Engines (Rule 1 & 2)';

    protected static ?string $icon = 'heroicon-o-list-bullet';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Rule Version & Control')
                    ->columns(3)
                    ->schema([
                        TextInput::make('rule_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Rule Set Name')
                            ->placeholder('e.g. Standard Adult Checklist & Eligibility'),

                        TextInput::make('version')
                            ->numeric()
                            ->default(1)
                            ->label('Version Number'),

                        Toggle::make('is_active')
                            ->label('Active for New Case Initializations')
                            ->default(true),
                    ]),

                Section::make('Dynamic Document Checklist Slots (Captured in Snapshot)')
                    ->description('Documents required from client when this service is initiated')
                    ->schema([
                        KeyValue::make('document_checklist_rules')
                            ->label('Document Checklist Items')
                            ->keyLabel('Slot Identifier (e.g. bank_stmt)')
                            ->valueLabel('Document Display Name (e.g. 6-Month Official Bank Statement)')
                            ->reorderable(),
                    ]),

                Section::make('Eligibility Criteria (No Hardcoding)')
                    ->schema([
                        KeyValue::make('eligibility_conditions')
                            ->label('Client Eligibility Thresholds')
                            ->keyLabel('Criteria (e.g. min_bank_balance_qar, min_qatar_residency_months)')
                            ->valueLabel('Requirement Value (e.g. 25,000 QAR, 6 months)')
                            ->reorderable(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('rule_name')
            ->columns([
                TextColumn::make('rule_name')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('version')
                    ->badge()
                    ->color('info')
                    ->prefix('v'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, H:i')
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
