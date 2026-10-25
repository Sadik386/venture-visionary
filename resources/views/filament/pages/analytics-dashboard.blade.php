<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Top Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Leads Ingested</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalLeads) }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-lg">
                        <x-heroicon-o-funnel class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs text-gray-500">
                    <span class="text-emerald-600 font-bold mr-1">{{ $leadConversionRate }}%</span> Conversion to Client
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Active Pipeline Cases</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($activeCases) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-lg">
                        <x-heroicon-o-briefcase class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs text-gray-500">
                    <span class="font-bold mr-1">{{ $totalCases }}</span> Total lifetime cases
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Revenue Invoiced</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">QAR {{ number_format($totalInvoiced, 2) }}</p>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-lg">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs text-gray-500">
                    <span class="text-emerald-600 font-bold mr-1">QAR {{ number_format($totalCollected, 2) }}</span> collected
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Outstanding Receivables</p>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">QAR {{ number_format($outstandingBalance, 2) }}</p>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-lg">
                        <x-heroicon-o-receipt-percent class="w-6 h-6" />
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs text-gray-500">
                    <span class="text-green-600 font-bold mr-1">{{ $approvedCases }}</span> Visas Approved
                </div>
            </div>
        </div>

        <!-- Middle Section: Top Destinations & Status Funnel -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Top Destinations -->
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-heroicon-o-globe-alt class="w-5 h-5 text-sky-500" />
                    Top Visa Destinations (Market Volume)
                </h3>
                <div class="space-y-3">
                    @forelse($countryCases as $c)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                        <span class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ $c->country_name ?? 'Unassigned' }}</span>
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300">
                            {{ $c->case_count }} {{ Str::plural('Case', $c->case_count) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No destination cases recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Case Lifecycle Distribution -->
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-heroicon-o-arrow-path class="w-5 h-5 text-indigo-500" />
                    Operational Case Pipeline Status
                </h3>
                <div class="space-y-3">
                    @php
                        $labels = [
                            'inquiry' => 'Initial Inquiries',
                            'documents_pending' => 'Document Collection',
                            'under_review' => 'Internal QC Review',
                            'appointment_booked' => 'VAC Appointment Booked',
                            'submitted' => 'Submitted to Embassy',
                            'decision_pending' => 'Decision Pending',
                            'approved' => 'Visas Approved & Issued',
                            'rejected' => 'Refused / Appeals',
                        ];
                    @endphp
                    @foreach($labels as $statusKey => $label)
                        @php $count = $statusCounts[$statusKey] ?? 0; @endphp
                        @if($count > 0 || in_array($statusKey, ['documents_pending', 'under_review', 'submitted', 'approved']))
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $count }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Governance & Audit Trail Stream (Rule 3) -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <x-heroicon-o-shield-check class="w-5 h-5 text-emerald-500" />
                Live Governance & Security Audit Stream (Section 26 Rule 3)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="py-2">Timestamp</th>
                            <th class="py-2">Actor</th>
                            <th class="py-2">Event</th>
                            <th class="py-2">Entity</th>
                            <th class="py-2">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($recentAudits as $audit)
                        <tr>
                            <td class="py-2.5 text-xs text-gray-500">{{ $audit->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-2.5 font-medium text-gray-900 dark:text-white">{{ $audit->user?->name ?? 'System Worker' }}</td>
                            <td class="py-2.5">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ ucfirst(str_replace('_', ' ', $audit->action)) }}
                                </span>
                            </td>
                            <td class="py-2.5 text-gray-600 dark:text-gray-400">{{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</td>
                            <td class="py-2.5 font-mono text-xs text-gray-500">{{ $audit->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500 text-xs">No audit events recorded yet. System activity will stream here live.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
