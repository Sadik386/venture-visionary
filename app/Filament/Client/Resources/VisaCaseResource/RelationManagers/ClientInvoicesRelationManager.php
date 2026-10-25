<?php

namespace App\Filament\Client\Resources\VisaCaseResource\RelationManagers;

use App\Models\Invoice;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class ClientInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Billing, Receipts & Invoices';

    protected static ?string $icon = 'heroicon-o-receipt-percent';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money(fn (Invoice $record) => $record->currency),

                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money(fn (Invoice $record) => $record->currency)
                    ->color('success'),

                TextColumn::make('balance_due')
                    ->label('Balance Due')
                    ->money(fn (Invoice $record) => $record->currency)
                    ->color(fn (Invoice $record) => (float) $record->balance_due > 0 ? 'danger' : 'success'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'partial' => 'warning',
                        'overdue' => 'danger',
                        default   => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->placeholder('Due on receipt'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([
                Action::make('download_invoice_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Invoice $record): string => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
