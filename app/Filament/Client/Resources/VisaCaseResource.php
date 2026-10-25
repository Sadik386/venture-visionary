<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\VisaCaseResource\Pages;
use App\Models\VisaCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class VisaCaseResource extends Resource
{
    protected static ?string $model = VisaCase::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $modelLabel = 'My Visa Case';

    protected static ?string $pluralModelLabel = 'My Visa Cases';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        if (! $user) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->whereHas('client', function ($q) use ($user) {
                $q->where('email', $user->email)
                    ->orWhere('phone', $user->phone)
                    ->orWhere('whatsapp', $user->whatsapp);
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Visa Application Overview')
                    ->description('Real-time progress of your application')
                    ->columns(3)
                    ->schema([
                        Placeholder::make('case_number')
                            ->label('Application Reference')
                            ->content(fn (VisaCase $record): string => $record->case_number ?? 'Pending'),

                        Placeholder::make('country_name')
                            ->label('Target Destination')
                            ->content(fn (VisaCase $record): string => $record->country?->name ?? 'N/A'),

                        Placeholder::make('service_name')
                            ->label('Service Track')
                            ->content(fn (VisaCase $record): string => $record->service?->name ?? 'Standard Track'),

                        Placeholder::make('client_status')
                            ->label('Application Progress')
                            ->content(fn (VisaCase $record): HtmlString => new HtmlString(
                                match ($record->status) {
                                    'documents_pending', 'inquiry' => '<span class="px-3 py-1 text-sm font-semibold text-yellow-800 bg-yellow-100 rounded-full">Documents Pending from Applicant</span>',
                                    'under_review' => '<span class="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-100 rounded-full">Under Verification & Review</span>',
                                    'appointment_booked' => '<span class="px-3 py-1 text-sm font-semibold text-indigo-800 bg-indigo-100 rounded-full">Appointment Booked at Visa Center</span>',
                                    'submitted' => '<span class="px-3 py-1 text-sm font-semibold text-purple-800 bg-purple-100 rounded-full">Submitted to Embassy</span>',
                                    'approved' => '<span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full">Visa Approved & Ready</span>',
                                    'rejected' => '<span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-100 rounded-full">Embassy Decision Issued</span>',
                                    default => '<span class="px-3 py-1 text-sm font-semibold text-gray-800 bg-gray-100 rounded-full">Processing</span>',
                                }
                            )),

                        Placeholder::make('intended_travel_date')
                            ->label('Planned Travel Date')
                            ->content(fn (VisaCase $record): string => $record->intended_travel_date ? $record->intended_travel_date->format('d M Y') : 'Not specified'),

                        Placeholder::make('balance_qar')
                            ->label('Remaining Balance')
                            ->content(fn (VisaCase $record): string => 'QAR ' . number_format((float) $record->balance_qar, 2)),
                    ]),

                Section::make('Consultant Guidance & Important Instructions')
                    ->description('Verified instructions from your assigned Venture Visionary case officer')
                    ->schema([
                        Textarea::make('client_notes')
                            ->label('Instructions for Applicant')
                            ->disabled()
                            ->rows(4)
                            ->placeholder('No specific instructions at this time.'),
                    ]),

                Section::make('Required Documents Checklist')
                    ->description('Mandatory documents required by the embassy for your application')
                    ->schema([
                        Placeholder::make('documents_list')
                            ->label('')
                            ->content(function (VisaCase $record): HtmlString {
                                $snapshot = $record->checklist_snapshot;
                                $docs = $snapshot['mandatory_documents'] ?? [];

                                if (empty($docs)) {
                                    return new HtmlString('<p class="text-sm text-gray-500">Standard documents required: Passport Copy, Qatar ID, 6-Month Bank Statement, Salary Certificate.</p>');
                                }

                                $html = '<div class="space-y-2">';
                                foreach ($docs as $code => $doc) {
                                    $name = htmlspecialchars($doc['name'] ?? $code);
                                    $status = htmlspecialchars($doc['status'] ?? 'pending');
                                    $badge = match ($status) {
                                        'verified' => '<span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded">Verified</span>',
                                        'uploaded' => '<span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">Under Review</span>',
                                        default => '<span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded">Required</span>',
                                    };
                                    $html .= "<div class='flex items-center justify-between p-2 border rounded-lg bg-gray-50'>
                                        <span class='text-sm font-medium text-gray-700'>{$name}</span>
                                        {$badge}
                                    </div>";
                                }
                                $html .= '</div>';

                                return new HtmlString($html);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('Application #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('country.name')
                    ->label('Destination')
                    ->badge()
                    ->color('info'),

                TextColumn::make('service.name')
                    ->label('Service Track'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'documents_pending', 'inquiry' => 'warning',
                        'under_review' => 'info',
                        'appointment_booked', 'submitted' => 'primary',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'documents_pending' => 'Action Needed',
                        'under_review' => 'Under Review',
                        'appointment_booked' => 'Appointment Booked',
                        'submitted' => 'Submitted to Embassy',
                        'approved' => 'Approved',
                        'rejected' => 'Decision Issued',
                        default => ucfirst($state),
                    }),

                TextColumn::make('intended_travel_date')
                    ->label('Travel Date')
                    ->date('d M Y')
                    ->placeholder('Not set'),

                TextColumn::make('balance_qar')
                    ->label('Balance Due')
                    ->money('QAR'),
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
            VisaCaseResource\RelationManagers\ClientDocumentsRelationManager::class,
            VisaCaseResource\RelationManagers\ClientTimelineRelationManager::class,
            VisaCaseResource\RelationManagers\ClientInvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisaCases::route('/'),
            'view' => Pages\ViewVisaCase::route('/{record}'),
        ];
    }
}
