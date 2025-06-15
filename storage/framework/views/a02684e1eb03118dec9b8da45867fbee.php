<div x-data="{ showModal: <?php if ((object) ('show') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('show'->value()); ?>')<?php echo e('show'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('show'); ?>')<?php endif; ?>.live }"
     x-show="showModal"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50"
     style="display: none;"
     @click.away="$wire.close()"
     @keydown.escape.window="$wire.close()">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-4xl relative flex flex-col max-h-[90vh]">
        <button type="button" @click="$wire.close()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!--[if BLOCK]><![endif]--><?php if($organization): ?>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center flex-shrink-0">
                Détails de l'Organisation: <?php echo e($organization->nom); ?>

            </h3>

            <div class="overflow-y-auto pr-2 flex-grow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100"><?php echo e($organization->nom); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">NIU:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100"><?php echo e($organization->NIU); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Type:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100"><?php echo e($organization->type); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création:</p>
                        <p class="text-base text-gray-900 dark:text-gray-100"><?php echo e($organization->date_creation->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut Validation:</p>
                        <p class="text-base">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php if($organization->validation_status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($organization->validation_status === 'accepted'): ?> bg-green-100 text-green-800
                                <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                <?php echo e(ucfirst($organization->validation_status)); ?>

                            </span>
                            <!--[if BLOCK]><![endif]--><?php if($organization->validation_status === 'rejected' && $organization->rejected_reason): ?>
                                <span class="block text-xs text-red-500 mt-1">Motif: <?php echo e($organization->rejected_reason); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut Activation:</p>
                        <p class="text-base">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php if($organization->activation_status === 'enabled'): ?> bg-green-100 text-green-800
                                <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                <?php echo e(ucfirst($organization->activation_status)); ?>

                            </span>
                            <!--[if BLOCK]><![endif]--><?php if($organization->activation_status === 'disabled' && $organization->disabled_reason): ?>
                                <span class="block text-xs text-red-500 mt-1">Motif: <?php echo e($organization->disabled_reason); ?> (Désactivé par: <?php echo e($organization->disabled_by_type); ?>)</span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </p>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($organization->organizer): ?>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Organisateur:</p>
                                <p class="text-base text-gray-900 dark:text-gray-100" >
                                    <button wire:click="showOrganizer('<?php echo e($organization->organizer->id); ?>')"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold underline transition-colors duration-150">
                                        <?php echo e($organization->organizer->nom ?? 'N/A'); ?> <?php echo e($organization->organizer->prenom ?? ''); ?>

                                    </button>
                                </p>
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if($showDetailModal): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.manage-organizers.organizer-details-modal' ,  ['organizerId' => $selectedOrganizerId]);

$__html = app('livewire')->mount($__name, $__params, $selectedOrganizerId, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 my-6 pt-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Historique des Statuts</h4>

                    
                    <div class="mb-4">
                        <label for="historySearch" class="sr-only">Rechercher dans l'historique</label>
                        <div class="relative">
                            <input type="text" id="historySearch" wire:model.live.debounce.300ms="historySearch" placeholder="Rechercher dans l'historique..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm
                                          dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                                          focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($statusHistory->isNotEmpty()): ?>
                        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('status_type')">
                                        Type
                                        <!--[if BLOCK]><![endif]--><?php if($historySortField === 'status_type'): ?>
                                            <span><?php echo $historySortDirection === 'asc' ? '&uarr;' : '&darr;'; ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('old_status')">
                                        Ancien Statut
                                        <!--[if BLOCK]><![endif]--><?php if($historySortField === 'old_status'): ?>
                                            <span><?php echo $historySortDirection === 'asc' ? '&uarr;' : '&darr;'; ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('new_status')">
                                        Nouveau Statut
                                        <!--[if BLOCK]><![endif]--><?php if($historySortField === 'new_status'): ?>
                                            <span><?php echo $historySortDirection === 'asc' ? '&uarr;' : '&darr;'; ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('reason')">
                                        Motif
                                        <!--[if BLOCK]><![endif]--><?php if($historySortField === 'reason'): ?>
                                            <span><?php echo $historySortDirection === 'asc' ? '&uarr;' : '&darr;'; ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider">
                                        Par
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-200 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortHistoryBy('created_at')">
                                        Date
                                        <!--[if BLOCK]><![endif]--><?php if($historySortField === 'created_at'): ?>
                                            <span><?php echo $historySortDirection === 'asc' ? '&uarr;' : '&darr;'; ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <?php echo e(ucfirst($history->status_type)); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <?php echo e(ucfirst($history->old_status)); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    <?php if($history->new_status === 'accepted' || $history->new_status === 'enabled'): ?> bg-green-100 text-green-800
                                                    <?php elseif($history->new_status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                                    <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                                    <?php echo e(ucfirst($history->new_status)); ?>

                                                </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            <?php echo e($history->reason ?: 'N/A'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <!--[if BLOCK]><![endif]--><?php if($history->changed_by_type === 'admin' ): ?>
                                                Admin: <?php echo e(\App\Models\Admin::withTrashed()->find($history->changed_by_id)->nom ?? 'Nom inconnu'); ?>

                                            <?php elseif($history->changed_by_type === 'organizer' ): ?>
                                                Organizer: <?php echo e(\App\Models\Organizer::withTrashed()->find($history->changed_by_id)->nom ?? 'Nom inconnu'); ?>

                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <?php echo e($history->created_at->format('d/m/Y H:i')); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-gray-500 dark:text-gray-400 mb-6">
                            <!--[if BLOCK]><![endif]--><?php if($historySearch): ?>
                                Aucun historique trouvé pour la recherche "<?php echo e($historySearch); ?>".
                            <?php else: ?>
                                Aucun historique de statut disponible pour cette organisation.
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="flex flex-wrap justify-center gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <!--[if BLOCK]><![endif]--><?php if(Auth::guard('admin')->user()->can('validate-organization') && $organization->validation_status === 'pending'): ?>
                        <button wire:click="acceptOrganization" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                            Valider l'Organisation
                        </button>
                        <button wire:click="openRejectReasonModal" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                            Rejeter l'Organisation
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php if(Auth::guard('admin')->user()->can('disable-organization') && $organization->activation_status === 'enabled' && $organization->validation_status === 'accepted'): ?>
                        <button wire:click="openDisableReasonModal" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-150">
                            Désactiver l'Organisation
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php if(Auth::guard('admin')->user()->can('enable-organization') && $organization->activation_status === 'disabled' && $organization->validation_status === 'accepted'): ?>
                        <button wire:click="openEnableReasonModal" class="px-5 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                            Activer l'Organisation
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div x-data="{ show: <?php if ((object) ('showRejectReasonModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showRejectReasonModal'->value()); ?>')<?php echo e('showRejectReasonModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showRejectReasonModal'); ?>')<?php endif; ?>.live }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Motif du Rejet</h3>
                        <div class="mb-4">
                            <label for="reject-reason-modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif:</label>
                            <textarea id="reject-reason-modal" wire:model.lazy="reason" rows="4"
                                      class="form-textarea block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="show = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Annuler
                            </button>
                            <button wire:click="rejectOrganization" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                Confirmer le Rejet
                            </button>
                        </div>
                    </div>
                </div>

                <div x-data="{ show: <?php if ((object) ('showDisableReasonModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDisableReasonModal'->value()); ?>')<?php echo e('showDisableReasonModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDisableReasonModal'); ?>')<?php endif; ?>.live }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Motif de la Désactivation</h3>
                        <div class="mb-4">
                            <label for="disable-reason-modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif:</label>
                            <textarea id="disable-reason-modal" wire:model.lazy="reason" rows="4"
                                      class="form-textarea block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="show = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Annuler
                            </button>
                            <button wire:click="disableOrganization" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                Confirmer la Désactivation
                            </button>
                        </div>
                    </div>
                </div>

                <div x-data="{ show: <?php if ((object) ('showEnableReasonModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showEnableReasonModal'->value()); ?>')<?php echo e('showEnableReasonModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showEnableReasonModal'); ?>')<?php endif; ?>.live }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Activer l'Organisation</h3>
                        <div class="mb-4">
                            <label for="enable-reason-modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif de l'activation (optionnel):</label>
                            <textarea id="enable-reason-modal" wire:model.lazy="reason" rows="4"
                                      class="form-textarea block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="show = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Annuler
                            </button>
                            <button wire:click="enableOrganization" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                Confirmer l'Activation
                            </button>
                        </div>
                    </div>
                </div>

                <?php else: ?>
                    <p class="text-center text-gray-500 dark:text-gray-400">Chargement des détails de l'organisation...</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/admin/manage-organizations/organization-details-modal.blade.php ENDPATH**/ ?>