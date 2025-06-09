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

    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6 sm:p-8 w-full max-w-2xl relative" @click.stop>
        
        <button type="button" @click="$wire.close()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!--[if BLOCK]><![endif]--><?php if($organization): ?>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">
                Détails de l'Organisation : <?php echo e($organization->nom); ?>

            </h2>

            
            <!--[if BLOCK]><![endif]--><?php if($errorMessage): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Erreur !</strong>
                    <span class="block sm:inline"><?php echo e($errorMessage); ?></span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="errorMessage = null">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Fermer</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="space-y-4 text-gray-700 dark:text-gray-300 mb-6">
                <p><strong>NIU:</strong> <?php echo e($organization->NIU); ?></p>
                <p><strong>Type:</strong> <?php echo e($organization->type); ?></p>
                <p><strong>Date de création:</strong> <?php echo e(\Carbon\Carbon::parse($organization->date_creation)->format('d/m/Y')); ?></p>
                <p><strong>Sous-domaine:</strong> <?php echo e($organization->domains->first()->domain ?? 'N/A'); ?></p>
                <p>
                    <strong>Statut de validation:</strong>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        <?php if($organization->validation_status === 'pending'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                        <?php elseif($organization->validation_status === 'accepted'): ?> bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                        <?php else: ?> bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 <?php endif; ?>">
                        <?php echo e(ucfirst($organization->validation_status)); ?>

                    </span>
                    <!--[if BLOCK]><![endif]--><?php if($organization->validation_status === 'rejected' && $organization->rejected_reason): ?>
                        <span class="text-red-500 text-sm ml-2">(<?php echo e($organization->rejected_reason); ?>)</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </p>
                <p>
                    <strong>Statut d'activation:</strong>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        <?php if($organization->activation_status === 'enabled'): ?> bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                        <?php else: ?> bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 <?php endif; ?>">
                        <?php echo e(ucfirst($organization->activation_status)); ?>

                    </span>
                    <!--[if BLOCK]><![endif]--><?php if($organization->activation_status === 'disabled' && $organization->disabled_reason): ?>
                        <span class="text-red-500 text-sm ml-2">(<?php echo e($organization->disabled_reason); ?>)</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </p>
            </div>

            
            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                <!--[if BLOCK]><![endif]--><?php if($organization->validation_status === 'accepted'): ?> 
                <!--[if BLOCK]><![endif]--><?php if($organization->activation_status === 'enabled'): ?>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Désactiver l'organisation</h3>
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif de la désactivation:</label>
                        <textarea id="reason" wire:model.lazy="reason" rows="3"
                                  class="form-textarea block w-full rounded-md border-gray-300 shadow-sm
                                             dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                                             focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50
                                             transition ease-in-out duration-150 p-2.5"
                                  placeholder="Ex: Arrêt temporaire des activités, maintenance..."></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <button wire:click="disableOrganization"
                            class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-600">
                        Désactiver l'organisation
                    </button>
                <?php elseif($organization->activation_status === 'disabled'): ?>
                    
                    <?php
                        $canEnable = app(App\Services\OrganizationStatusService::class)->canOrganizerEnable($organization, Auth::guard('organizer')->user());
                    ?>

                    <!--[if BLOCK]><![endif]--><?php if($canEnable): ?>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Réactiver l'organisation</h3>
                        <button wire:click="enableOrganization"
                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-800 dark:focus:ring-green-600">
                            Activer l'organisation
                        </button>
                    <?php else: ?>
                        <p class="text-yellow-600 dark:text-yellow-400 text-sm">
                            Cette organisation a été désactivée par un administrateur et ne peut pas être réactivée par vous.
                        </p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php else: ?>
                    <p class="text-gray-600 dark:text-gray-400 text-sm text-center">
                        L'activation/désactivation est disponible une fois l'organisation <span class="font-bold">acceptée</span>.
                    </p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

        <?php else: ?>
            <p class="text-center text-gray-500 dark:text-gray-400">Chargement des détails de l'organisation ou organisation introuvable.</p>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/organization/organizer/manage-organizations/organization-details-modal.blade.php ENDPATH**/ ?>