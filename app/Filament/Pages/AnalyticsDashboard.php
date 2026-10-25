<?php

namespace App\Filament\Pages;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\VisaCase;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Governance & Audit';

    protected static ?string $navigationLabel = 'Executive Analytics & BI';

    protected static ?string $title = 'Executive Intelligence & Operational Reports';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.analytics-dashboard';

    public function getViewData(): array
    {
        $totalLeads = Lead::count();
        $convertedLeads = Lead::where('status', 'converted')->count();
        $leadConversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        $totalClients = Client::count();
        $totalCases = VisaCase::count();
        $approvedCases = VisaCase::where('status', 'approved')->count();
        $activeCases = VisaCase::whereNotIn('status', ['approved', 'rejected', 'closed'])->count();

        // Financials (QAR converted)
        $totalInvoiced = (float) Invoice::sum('total_amount');
        $totalCollected = (float) Invoice::sum('paid_amount');
        $outstandingBalance = (float) Invoice::sum('balance_due');

        // Country distribution
        $countryCases = VisaCase::select('countries.name as country_name', DB::raw('count(cases.id) as case_count'))
            ->leftJoin('countries', 'cases.country_id', '=', 'countries.id')
            ->groupBy('countries.name')
            ->orderByDesc('case_count')
            ->limit(6)
            ->get();

        // Status breakdown
        $statusCounts = VisaCase::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent Audit activities
        $recentAudits = AuditLog::with('user')->latest()->limit(5)->get();

        return [
            'totalLeads'          => $totalLeads,
            'convertedLeads'      => $convertedLeads,
            'leadConversionRate'  => $leadConversionRate,
            'totalClients'        => $totalClients,
            'totalCases'          => $totalCases,
            'activeCases'         => $activeCases,
            'approvedCases'       => $approvedCases,
            'totalInvoiced'       => $totalInvoiced,
            'totalCollected'      => $totalCollected,
            'outstandingBalance'  => $outstandingBalance,
            'countryCases'        => $countryCases,
            'statusCounts'        => $statusCounts,
            'recentAudits'        => $recentAudits,
        ];
    }
}
