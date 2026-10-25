<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Models\Quotation;
use App\Models\Client;
use App\Models\VisaCase;
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

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Finance & Commercial';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Quotation Details')
                    ->description('Client reference and validity')
                    ->columns(3)
                    ->schema([
                        TextInput::make('quotation_number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated (e.g. QT-2026-0001)'),

                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('case_id')
                            ->label('Associated Visa Case (Optional)')
                            ->relationship('visaCase', 'case_number')
                            ->searchable()
                            ->preload(),

                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent to Client',
                                'accepted' => 'Accepted by Client',
                                'rejected' => 'Rejected',
                                'expired' => 'Expired',
                            ])
                            ->default('draft')
                            ->required(),

                        DatePicker::make('valid_until')
                            ->label('Valid Until')
                            ->default(now()->addDays(14)),

                        Select::make('currency')
                            ->options([
                                'QAR' => 'QAR (Qatari Riyal)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                                'GBP' => 'GBP (British Pound)',
                            ])
                            ->default('QAR')
                            ->required(),
                    ]),

                Section::make('Itemized Fee Breakdown (Rule 10: Fee Transparency)')
                    ->description('Separate government fees, VAC charges, and consultancy fees')
                    ->columns(3)
                    ->schema([
                        TextInput::make('government_fee')
                            ->numeric()
                            ->default(0.00)
                            ->label('Embassy / Government Fee')
                            ->prefix('QAR')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotalAmount($set, $get)),

                        TextInput::make('vac_fee')
                            ->numeric()
                            ->default(0.00)
                            ->label('VAC / TLS / VFS Center Fee')
                            ->prefix('QAR')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotalAmount($set, $get)),

                        TextInput::make('consultancy_fee')
                            ->numeric()
                            ->default(0.00)
                            ->label('Venture Visionary Consultancy Fee')
                            ->prefix('QAR')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotalAmount($set, $get)),

                        TextInput::make('other_charges')
                            ->numeric()
                            ->default(0.00)
                            ->label('Translation / Courier / Other')
                            ->prefix('QAR')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotalAmount($set, $get)),

                        TextInput::make('discount_amount')
                            ->numeric()
                            ->default(0.00)
                            ->label('Discount / Promotional Deduction')
                            ->prefix('QAR')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotalAmount($set, $get)),

                        TextInput::make('total_amount')
                            ->numeric()
                            ->label('Final Payable Total')
                            ->prefix('QAR')
                            ->readOnly(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotation_number')
                    ->label('Quote #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('client.full_name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'accepted' => 'success',
                        'rejected', 'expired' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('total_amount')
                    ->label('Total Fee')
                    ->money(fn (Quotation $record) => $record->currency)
                    ->sortable(),

                TextColumn::make('valid_until')
                    ->date('d M Y')
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
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'expired' => 'Expired',
                    ]),
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Quotation $record): string => route('quotations.pdf', $record))
                    ->openUrlInNewTab(),
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

    public static function updateTotalAmount(Forms\Set $set, Forms\Get $get): void
    {
        $gov = (float) $get('government_fee');
        $vac = (float) $get('vac_fee');
        $consultancy = (float) $get('consultancy_fee');
        $other = (float) $get('other_charges');
        $discount = (float) $get('discount_amount');

        $total = max(0, $gov + $vac + $consultancy + $other - $discount);
        $set('total_amount', number_format($total, 2, '.', ''));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
