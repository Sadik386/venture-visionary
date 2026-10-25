<x-filament-panels::page>
    <div x-data="{
        draggingId: null,
        handleDragStart(e, id) {
            this.draggingId = id;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', id);
        },
        handleDrop(e, targetStage) {
            e.preventDefault();
            const id = this.draggingId || e.dataTransfer.getData('text/plain');
            if (id) {
                $wire.updateLeadStage(id, targetStage);
            }
            this.draggingId = null;
        }
    }" class="space-y-6">

        {{-- GoHighLevel Smart Lists & Quick Filters --}}
        <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Smart Lists:</span>
                
                <button type="button" wire:click="setFilter('all')"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors {{ $activeFilter === 'all' ? 'bg-amber-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    All Leads
                </button>
                
                <button type="button" wire:click="setFilter('hot')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors {{ $activeFilter === 'hot' ? 'bg-rose-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    <span>🔥</span> Hot Leads (≥70)
                </button>
                
                <button type="button" wire:click="setFilter('follow_up')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors {{ $activeFilter === 'follow_up' ? 'bg-orange-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    <span>⏰</span> Follow-up Needed
                </button>

                <button type="button" wire:click="setFilter('unassigned')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors {{ $activeFilter === 'unassigned' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    <span>👤</span> Unassigned
                </button>

                <button type="button" wire:click="setFilter('high_intent')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors {{ $activeFilter === 'high_intent' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    <span>⭐</span> VIP Destinations
                </button>
            </div>

            {{-- Search & Destination Filter & Switch to Table --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search leads, phone, #..."
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>

                <select wire:model.live="destinationFilter"
                    class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">All Destinations</option>
                    @foreach($this->destinations as $dest)
                        <option value="{{ $dest }}">{{ $dest }}</option>
                    @endforeach
                </select>

                <a href="/staff/leads" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    <x-heroicon-m-table-cells class="h-4 w-4" />
                    Table View
                </a>
            </div>
        </div>

        {{-- Visual Drag-and-Drop Columns --}}
        <div class="flex gap-4 overflow-x-auto pb-6">
            @php $groupedLeads = $this->leadsGroupedByStage; @endphp

            @foreach($stages as $stageKey => $stageMeta)
                @php $leadsInStage = $groupedLeads[$stageKey] ?? collect(); @endphp
                <div class="flex w-80 shrink-0 flex-col rounded-xl border border-gray-200 bg-gray-50/70 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
                    @dragover.prevent
                    @drop="handleDrop($event, '{{ $stageKey }}')">
                    
                    {{-- Column Header --}}
                    <div class="border-b border-gray-200 p-3 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full {{ $stageMeta['bar'] }}"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100">
                                    {{ $stageMeta['name'] }}
                                </h3>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $stageMeta['badge'] }}">
                                {{ $leadsInStage->count() }}
                            </span>
                        </div>
                    </div>

                    {{-- Cards Container --}}
                    <div class="flex flex-1 flex-col gap-3 p-3 min-h-[300px]">
                        @forelse($leadsInStage as $lead)
                            <div class="group relative rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition hover:shadow-md dark:border-gray-700/80 dark:bg-gray-800 cursor-grab active:cursor-grabbing"
                                draggable="true"
                                @dragstart="handleDragStart($event, '{{ $lead->id }}')">

                                {{-- Card Top: Lead # & Score --}}
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-mono font-bold text-amber-600 dark:text-amber-400">
                                        {{ $lead->lead_number }}
                                    </span>
                                    
                                    @php
                                        $score = $lead->lead_score ?? 50;
                                        $scoreColor = $score >= 70 ? 'text-emerald-700 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-300' : ($score >= 40 ? 'text-amber-700 bg-amber-50 dark:bg-amber-950/60 dark:text-amber-300' : 'text-rose-700 bg-rose-50 dark:bg-rose-950/60 dark:text-rose-300');
                                    @endphp
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $scoreColor }}">
                                        Score: {{ $score }}/100
                                    </span>
                                </div>

                                {{-- Prospect Name --}}
                                <div class="mt-1">
                                    <a href="/staff/leads/{{ $lead->id }}" class="text-sm font-semibold text-gray-900 hover:text-amber-600 dark:text-gray-100 dark:hover:text-amber-400">
                                        {{ $lead->name }}
                                    </a>
                                </div>

                                {{-- Destination & Service --}}
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @if($lead->destination_country)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            🌍 {{ $lead->destination_country }}
                                        </span>
                                    @endif
                                    @if($lead->visa_or_service_type)
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $lead->visa_or_service_type }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Contact & WhatsApp Quick Action --}}
                                <div class="mt-2.5 flex items-center justify-between border-t border-gray-100 pt-2 text-xs text-gray-500 dark:border-gray-700/60 dark:text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <span class="truncate max-w-[120px]">{{ $lead->assignedTo?->name ?? 'Unassigned' }}</span>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        @if($lead->whatsapp || $lead->phone)
                                            @php $phoneDigits = preg_replace('/[^0-9]/', '', $lead->whatsapp ?? $lead->phone); @endphp
                                            <a href="https://wa.me/{{ $phoneDigits }}" target="_blank" title="Direct WhatsApp Chat"
                                                class="rounded p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/60">
                                                <x-heroicon-m-chat-bubble-left-ellipsis class="h-4 w-4" />
                                            </a>
                                        @endif

                                        <a href="/staff/leads/{{ $lead->id }}/edit" title="Edit Lead"
                                            class="rounded p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <x-heroicon-m-pencil-square class="h-4 w-4" />
                                        </a>
                                    </div>
                                </div>

                                {{-- Quick Stage Changer & Convert Action --}}
                                <div class="mt-2 flex items-center justify-between text-[11px]">
                                    <select wire:change="updateLeadStage('{{ $lead->id }}', $event.target.value)"
                                        class="rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[11px] text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        @foreach($stages as $sk => $sm)
                                            <option value="{{ $sk }}" {{ $lead->status === $sk ? 'selected' : '' }}>
                                                Move: {{ $sm['name'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if($lead->status !== 'converted')
                                        <button type="button" wire:click="convertToClient('{{ $lead->id }}')"
                                            class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                            + Client 360
                                        </button>
                                    @else
                                        <span class="text-emerald-600 font-medium">✓ Client</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="flex h-32 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 text-xs text-gray-400 dark:border-gray-800 dark:text-gray-500">
                                No leads in this stage
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
