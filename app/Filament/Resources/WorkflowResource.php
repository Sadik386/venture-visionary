<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowResource\Pages;
use App\Filament\Resources\WorkflowResource\RelationManagers;
use App\Models\Workflow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Automations & Workflows';

    protected static ?string $navigationLabel = 'Workflow Engine';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Workflow Definition & Trigger')
                    ->description('Specify the business event that triggers this automated workflow')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Instant WhatsApp Welcome for New Inquiries'),

                        Select::make('trigger_event')
                            ->label('Triggering Event')
                            ->required()
                            ->options([
                                'lead.created' => '⚡ Lead Created (Website / WhatsApp / Meta Inbound)',
                                'lead.stage_changed' => '🔄 Lead Stage Transitioned',
                                'appointment.scheduled' => '📅 Appointment Booked / Scheduled',
                                'appointment.completed' => '✅ Appointment Completed / Attended',
                                'case.status_changed' => '🛡️ Visa Case Status Changed (e.g. Approved)',
                            ]),

                        Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(2)
                            ->placeholder('Describe what business logic this workflow carries out...'),

                        Toggle::make('is_active')
                            ->label('Workflow Enabled')
                            ->default(true),
                    ]),

                Section::make('Trigger Conditions (Optional Filter)')
                    ->description('Leave blank to run on all events, or specify criteria (e.g. status => qualified, lead_score_gte => 70)')
                    ->schema([
                        KeyValue::make('conditions')
                            ->label('Matching Conditions')
                            ->keyLabel('Condition Attribute')
                            ->valueLabel('Expected Value')
                            ->helperText('Supported keys: status, lead_score_gte, destination_country, is_duplicate'),
                    ]),

                Section::make('Sequential Automation Actions')
                    ->description('Ordered steps executed when triggers and conditions are fulfilled')
                    ->schema([
                        Repeater::make('actions')
                            ->relationship('actions')
                            ->schema([
                                Select::make('action_type')
                                    ->label('Action Type')
                                    ->required()
                                    ->options([
                                        'create_task' => '📝 Create Operational Task for Staff',
                                        'send_notification' => '🔔 Send Staff Database Notification',
                                        'send_whatsapp' => '💬 Send Automated WhatsApp Message',
                                        'assign_agent' => '👤 Reassign Sales / Visa Agent',
                                        'trigger_review_request' => '⭐ Dispatch 5-Star Review Request',
                                        'trigger_webhook' => '🌐 Send Webhook to External Integration',
                                    ]),

                                KeyValue::make('action_config')
                                    ->label('Action Configuration')
                                    ->keyLabel('Parameter')
                                    ->valueLabel('Value')
                                    ->helperText('Examples: title, description, due_days, message, priority'),

                                Toggle::make('is_active')
                                    ->label('Step Active')
                                    ->default(true),
                            ])
                            ->orderColumn('order')
                            ->collapsible()
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Workflow Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('trigger_event')
                    ->label('Trigger Event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lead.created' => 'info',
                        'lead.stage_changed' => 'warning',
                        'appointment.scheduled', 'appointment.completed' => 'primary',
                        'case.status_changed' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('actions_count')
                    ->counts('actions')
                    ->label('Steps')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('execution_count')
                    ->label('Total Executions')
                    ->sortable()
                    ->alignEnd(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('last_triggered_at')
                    ->label('Last Run')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflows::route('/'),
            'create' => Pages\CreateWorkflow::route('/create'),
            'edit' => Pages\EditWorkflow::route('/{record}/edit'),
        ];
    }
}
