<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Client;
use App\Models\VisaCase;
use App\Models\Partner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeLeads = Lead::whereNotIn('status', ['converted', 'lost', 'duplicate'])->count();
        $activeCases = VisaCase::whereNotIn('status', ['approved', 'closed', 'rejected'])->count();
        $totalClients = Client::count();
        $totalPipelineQar = VisaCase::sum('total_amount_qar');

        return [
            Stat::make('Active Inbound Leads', $activeLeads)
                ->description('Pending consultation & qualification')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->chart([7, 4, 6, 8, 5, 9, $activeLeads ?: 1]),

            Stat::make('Active Visa Cases', $activeCases)
                ->description('Under review or submitted to embassy')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning')
                ->chart([3, 5, 4, 7, 6, 8, $activeCases ?: 1]),

            Stat::make('Master Clients 360', $totalClients)
                ->description('Registered Qatar individuals & VIPs')
                ->descriptionIcon('heroicon-m-identification')
                ->color('success'),

            Stat::make('Pipeline Value (QAR)', 'QAR ' . number_format($totalPipelineQar, 2))
                ->description('Total agreed service & visa revenue')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }
}
