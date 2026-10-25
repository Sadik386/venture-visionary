<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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

        
        <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Smart Lists:</span>
                
                <button type="button" wire:click="setFilter('all')"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors <?php echo e($activeFilter === 'all' ? 'bg-amber-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'); ?>">
                    All Leads
                </button>
                
                <button type="button" wire:click="setFilter('hot')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors <?php echo e($activeFilter === 'hot' ? 'bg-rose-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'); ?>">
                    <span>🔥</span> Hot Leads (≥70)
                </button>
                
                <button type="button" wire:click="setFilter('follow_up')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors <?php echo e($activeFilter === 'follow_up' ? 'bg-orange-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'); ?>">
                    <span>⏰</span> Follow-up Needed
                </button>

                <button type="button" wire:click="setFilter('unassigned')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors <?php echo e($activeFilter === 'unassigned' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'); ?>">
                    <span>👤</span> Unassigned
                </button>

                <button type="button" wire:click="setFilter('high_intent')"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors <?php echo e($activeFilter === 'high_intent' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'); ?>">
                    <span>⭐</span> VIP Destinations
                </button>
            </div>

            
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search leads, phone, #..."
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>

                <select wire:model.live="destinationFilter"
                    class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">All Destinations</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->destinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($dest); ?>"><?php echo e($dest); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>

                <a href="/staff/leads" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-table-cells'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    Table View
                </a>
            </div>
        </div>

        
        <div class="flex gap-4 overflow-x-auto pb-6">
            <?php $groupedLeads = $this->leadsGroupedByStage; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stageKey => $stageMeta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $leadsInStage = $groupedLeads[$stageKey] ?? collect(); ?>
                <div class="flex w-80 shrink-0 flex-col rounded-xl border border-gray-200 bg-gray-50/70 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
                    @dragover.prevent
                    @drop="handleDrop($event, '<?php echo e($stageKey); ?>')">
                    
                    
                    <div class="border-b border-gray-200 p-3 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full <?php echo e($stageMeta['bar']); ?>"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100">
                                    <?php echo e($stageMeta['name']); ?>

                                </h3>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-bold <?php echo e($stageMeta['badge']); ?>">
                                <?php echo e($leadsInStage->count()); ?>

                            </span>
                        </div>
                    </div>

                    
                    <div class="flex flex-1 flex-col gap-3 p-3 min-h-[300px]">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leadsInStage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="group relative rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition hover:shadow-md dark:border-gray-700/80 dark:bg-gray-800 cursor-grab active:cursor-grabbing"
                                draggable="true"
                                @dragstart="handleDragStart($event, '<?php echo e($lead->id); ?>')">

                                
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-mono font-bold text-amber-600 dark:text-amber-400">
                                        <?php echo e($lead->lead_number); ?>

                                    </span>
                                    
                                    <?php
                                        $score = $lead->lead_score ?? 50;
                                        $scoreColor = $score >= 70 ? 'text-emerald-700 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-300' : ($score >= 40 ? 'text-amber-700 bg-amber-50 dark:bg-amber-950/60 dark:text-amber-300' : 'text-rose-700 bg-rose-50 dark:bg-rose-950/60 dark:text-rose-300');
                                    ?>
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold <?php echo e($scoreColor); ?>">
                                        Score: <?php echo e($score); ?>/100
                                    </span>
                                </div>

                                
                                <div class="mt-1">
                                    <a href="/staff/leads/<?php echo e($lead->id); ?>" class="text-sm font-semibold text-gray-900 hover:text-amber-600 dark:text-gray-100 dark:hover:text-amber-400">
                                        <?php echo e($lead->name); ?>

                                    </a>
                                </div>

                                
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->destination_country): ?>
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            🌍 <?php echo e($lead->destination_country); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->visa_or_service_type): ?>
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            <?php echo e($lead->visa_or_service_type); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div class="mt-2.5 flex items-center justify-between border-t border-gray-100 pt-2 text-xs text-gray-500 dark:border-gray-700/60 dark:text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <span class="truncate max-w-[120px]"><?php echo e($lead->assignedTo?->name ?? 'Unassigned'); ?></span>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->whatsapp || $lead->phone): ?>
                                            <?php $phoneDigits = preg_replace('/[^0-9]/', '', $lead->whatsapp ?? $lead->phone); ?>
                                            <a href="https://wa.me/<?php echo e($phoneDigits); ?>" target="_blank" title="Direct WhatsApp Chat"
                                                class="rounded p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/60">
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-chat-bubble-left-ellipsis'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <a href="/staff/leads/<?php echo e($lead->id); ?>/edit" title="Edit Lead"
                                            class="rounded p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-pencil-square'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                        </a>
                                    </div>
                                </div>

                                
                                <div class="mt-2 flex items-center justify-between text-[11px]">
                                    <select wire:change="updateLeadStage('<?php echo e($lead->id); ?>', $event.target.value)"
                                        class="rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[11px] text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk => $sm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sk); ?>" <?php echo e($lead->status === $sk ? 'selected' : ''); ?>>
                                                Move: <?php echo e($sm['name']); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status !== 'converted'): ?>
                                        <button type="button" wire:click="convertToClient('<?php echo e($lead->id); ?>')"
                                            class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                            + Client 360
                                        </button>
                                    <?php else: ?>
                                        <span class="text-emerald-600 font-medium">✓ Client</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="flex h-32 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 text-xs text-gray-400 dark:border-gray-800 dark:text-gray-500">
                                No leads in this stage
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/filament/pages/lead-pipeline-board.blade.php ENDPATH**/ ?>