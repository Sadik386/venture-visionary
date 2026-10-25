<?php

namespace App\Filament\Partner\Widgets;

use App\Models\Partner;
use App\Models\PartnerUser;
use App\Models\PartnerWallet;
use App\Models\VisaCase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PartnerStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $partnerUser = PartnerUser::where('email', $user?->email)->first();
        $partner = $partnerUser ? Partner::find($partnerUser->partner_id) : null;
        $wallet = $partner ? PartnerWallet::where('partner_id', $partner->id)->first() : null;

        $creditLimit = $partner ? (float) $partner->credit_limit_qar : 0.00;
        $walletBalance = $wallet ? (float) $wallet->balance : 0.00;

        $activeCases = $partner ? VisaCase::where('partner_id', $partner->id)
            ->whereNotIn('status', ['approved', 'closed', 'rejected'])
            ->count() : 0;

        $approvedCases = $partner ? VisaCase::where('partner_id', $partner->id)
            ->whereIn('status', ['approved', 'closed'])
            ->count() : 0;

        return [
            Stat::make('Approved Credit Facility', 'QAR ' . number_format($creditLimit, 2))
                ->description('Authorized revolving credit limit')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),

            Stat::make('Wallet Prepaid Balance', 'QAR ' . number_format($walletBalance, 2))
                ->description('Available balance for new filings')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success'),

            Stat::make('Active Agency Filings', $activeCases)
                ->description('Under embassy or QC processing')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Visas Issued & Completed', $approvedCases)
                ->description('Delivered successfully to agency')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info'),
        ];
    }
}
