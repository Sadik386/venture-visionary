<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\VisaCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Models\Payment;
use App\Models\TimelineEvent;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Finance & Commercial';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Invoice Details')
                    ->description('Billing reference and client association')
                    ->columns(3)
                    ->schema([
                        TextInput::make('invoice_number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated (e.g. INV-2026-0001)'),

                        Select::make('client_id')
                            ->label('Bill To (Client)')
                            ->relationship('client', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('case_id')
                            ->label('Associated Visa Case')
                            ->relationship('visaCase', 'case_number')
                            ->searchable()
                            ->preload(),

                        Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'sent'      => 'Sent to Client',
                                'partial'   => 'Partially Paid',
                                'paid'      => 'Fully Paid',
                                'overdue'   => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('sent')
                            ->required(),

                        DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->default(now()->addDays(7))
                            ->required(),

                        Select::make('currency')
                            ->options([
                                'QAR' => 'QAR (Qatari Riyal)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                                'GBP' => 'GBP (British Pound)',
                                'BDT' => 'BDT (Bangladesh Taka)',
                            ])
                            ->default('QAR')
                            ->required(),

                        TextInput::make('exchange_rate_to_qar')
                            ->label('Exchange Rate to QAR')
                            ->numeric()
                            ->default(1.0000)
                            ->step(0.0001),
                    ]),

                Section::make('Financial Summary (Rule 10: Fee Transparency)')
                    ->description('Itemized fee breakdown — government, VAC, and consultancy must be tracked separately')
                    ->columns(4)
                    ->schema([
                        TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('QAR')
                            ->default(0.00)
                            ->label('Subtotal')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateBalanceDue($set, $get)),

                        TextInput::make('tax_amount')
                            ->numeric()
                            ->prefix('QAR')
                            ->default(0.00)
                            ->label('Tax / VAT')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateBalanceDue($set, $get)),

                        TextInput::make('discount_amount')
                            ->numeric()
                            ->prefix('QAR')
                            ->default(0.00)
                            ->label('Discount')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateBalanceDue($set, $get)),

                        TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('QAR')
                            ->label('Invoice Total')
                            ->readOnly()
                            ->live(onBlur: true),

                        TextInput::make('paid_amount')
                            ->numeric()
                            ->prefix('QAR')
                            ->default(0.00)
                            ->label('Amount Paid')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateBalanceDue($set, $get)),

                        TextInput::make('balance_due')
                            ->numeric()
                            ->prefix('QAR')
                            ->label('Balance Due')
                            ->readOnly(),
                    ]),

                Section::make('Line Items')
                    ->description('Itemize charges with fee categories for transparent audit trail')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                TextInput::make('description')
                                    ->required()
                                    ->columnSpan(3),

                                Select::make('fee_category')
                                    ->options([
                                        'government'  => 'Embassy / Government Fee',
                                        'service'     => 'Consultancy Service Fee',
                                        'vac'         => 'VAC / VFS / TLScontact Fee',
                                        'translation' => 'Document Translation',
                                        'courier'     => 'Courier & Delivery',
                                        'medical'     => 'Medical / Insurance',
                                        'other'       => 'Other Charges',
                                    ])
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        $qty = (int) $get('quantity');
                                        $unit = (float) $get('unit_amount');
                                        $set('total_amount', number_format($qty * $unit, 2, '.', ''));
                                    }),

                                TextInput::make('unit_amount')
                                    ->numeric()
                                    ->prefix('QAR')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        $qty = (int) $get('quantity');
                                        $unit = (float) $get('unit_amount');
                                        $set('total_amount', number_format($qty * $unit, 2, '.', ''));
                                    }),

                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->prefix('QAR')
                                    ->readOnly(),
                            ])
                            ->columns(8)
                            ->addActionLabel('Add Line Item')
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function updateBalanceDue(Forms\Set $set, Forms\Get $get): void
    {
        $subtotal  = (float) $get('subtotal');
        $tax       = (float) $get('tax_amount');
        $discount  = (float) $get('discount_amount');
        $paid      = (float) $get('paid_amount');

        $total   = max(0, $subtotal + $tax - $discount);
        $balance = max(0, $total - $paid);

        $set('total_amount', number_format($total, 2, '.', ''));
        $set('balance_due', number_format($balance, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visaCase.case_number')
                    ->label('Case Ref')
                    ->placeholder('No case linked'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'sent'      => 'info',
                        'partial'   => 'warning',
                        'paid'      => 'success',
                        'overdue'   => 'danger',
                        'cancelled' => 'secondary',
                        default     => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money(fn (Invoice $record) => $record->currency)
                    ->sortable(),

                TextColumn::make('balance_due')
                    ->label('Balance Due')
                    ->money(fn (Invoice $record) => $record->currency)
                    ->color(fn (Invoice $record) => (float) $record->balance_due > 0 ? 'danger' : 'success'),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Invoice $record) => $record->due_date->isPast() && $record->status !== 'paid' ? 'danger' : null),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'sent'      => 'Sent',
                        'partial'   => 'Partial',
                        'paid'      => 'Paid',
                        'overdue'   => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Invoice $record): string => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('record_payment')
                    ->label('Pay')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->visible(fn (Invoice $record) => (float) $record->balance_due > 0)
                    ->form([
                        TextInput::make('amount')
                            ->label('Payment Amount')
                            ->numeric()
                            ->required()
                            ->default(fn (Invoice $record) => (float) $record->balance_due),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer (QNB / CBQ)',
                                'credit_card'   => 'Credit / Debit Card (POS/Gateway)',
                                'cash'          => 'Cash Receipt',
                                'cheque'        => 'Company Cheque',
                            ])
                            ->default('bank_transfer')
                            ->required(),

                        TextInput::make('transaction_reference')
                            ->label('Transaction Reference / Cheque #')
                            ->placeholder('e.g. QNB-TRX-948201')
                            ->required(),

                        DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->default(now())
                            ->required(),

                        Textarea::make('notes')
                            ->label('Payment Notes')
                            ->rows(2),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        $amount = (float) $data['amount'];
                        $receiptNum = 'RCP-' . now()->format('Y') . '-' . strtoupper(substr(uniqid(), -5));

                        Payment::create([
                            'invoice_id' => $record->id,
                            'case_id' => $record->case_id,
                            'receipt_number' => $receiptNum,
                            'amount' => $amount,
                            'currency' => $record->currency,
                            'exchange_rate_to_qar' => (float) $record->exchange_rate_to_qar,
                            'amount_in_qar' => $amount * (float) $record->exchange_rate_to_qar,
                            'payment_method' => $data['payment_method'],
                            'transaction_reference' => $data['transaction_reference'],
                            'payment_date' => $data['payment_date'],
                            'received_by_id' => Auth::id(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        $newPaid = (float) $record->paid_amount + $amount;
                        $total = (float) $record->total_amount;
                        $newBalance = max(0, $total - $newPaid);
                        $newStatus = $newBalance <= 0 ? 'paid' : 'partial';

                        $record->update([
                            'paid_amount' => $newPaid,
                            'balance_due' => $newBalance,
                            'status' => $newStatus,
                        ]);

                        if ($record->case_id && $record->visaCase) {
                            $case = $record->visaCase;
                            $case->increment('paid_amount_qar', $amount * (float) $record->exchange_rate_to_qar);
                            $case->balance_qar = max(0, (float) $case->total_amount_qar - (float) $case->paid_amount_qar);
                            $case->save();

                            TimelineEvent::create([
                                'case_id' => $case->id,
                                'actor_id' => Auth::id(),
                                'event_type' => 'payment_recorded',
                                'title' => "Payment of {$record->currency} {$amount} Recorded",
                                'description' => "Receipt #{$receiptNum} via " . ucfirst(str_replace('_', ' ', $data['payment_method'])),
                                'is_client_visible' => true,
                            ]);
                        }

                        Notification::make()
                            ->title("Payment Recorded ({$receiptNum})")
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
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
