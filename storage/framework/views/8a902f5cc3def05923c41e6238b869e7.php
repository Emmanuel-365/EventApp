
<div x-data="{ show: <?php if ((object) ('showModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'->value()); ?>')<?php echo e('showModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'); ?>')<?php endif; ?>, tab: 'details' }"
     x-cloak 
     x-init="
        // Écoute l'événement Livewire pour changer l'onglet de la modale
        Livewire.on('setEditModalTab', tabName => {
            tab = tabName;
        });
     "
>
    
    <div x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        
        <div x-show="show" @click="show = false" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

        
        <div x-show="show" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full p-6 relative z-10 max-h-[95vh] overflow-y-auto">
            
            <!--[if BLOCK]><![endif]--><?php if(!$event): ?>
                <p class="text-center text-lg text-gray-600 dark:text-gray-400 py-10">Chargement de l'événement...</p>
            <?php else: ?>
                
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
                    Modifier Événement: <span class="text-blue-600 dark:text-blue-400"><?php echo e($event->title); ?></span>
                </h3>

                
                <button @click="show = false" type="button"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                
                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="tab = 'details'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': tab === 'details', 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'details' }"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 focus:outline-none">
                            Détails de l'événement
                        </button>
                        <button @click="tab = 'status'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': tab === 'status', 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'status' }"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 focus:outline-none">
                            Gestion du Statut
                        </button>
                        <button @click="tab = 'history'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': tab === 'history', 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'history' }"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 focus:outline-none">
                            Historique des Changements
                        </button>
                    </nav>
                </div>

                
                <div x-show="tab === 'details'">
                    <!--[if BLOCK]><![endif]--><?php if($canUpdateEvents): ?>
                        <form wire:submit.prevent="updateEvent">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="edit-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titre de l'événement <span class="text-red-500">*</span></label>
                                    <input type="text" id="edit-title" wire:model.live="title"
                                           class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div>
                                    <label for="edit-location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lieu <span class="text-red-500">*</span></label>
                                    <input type="text" id="edit-location" wire:model.live="location"
                                           class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div>
                                    <label for="edit-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                                    <input type="date" id="edit-date" wire:model.live="date"
                                           class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div>
                                    <label for="edit-time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Heure <span class="text-red-500">*</span></label>
                                    <input type="time" id="edit-time" wire:model.live="time"
                                           class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div>
                                    <label for="edit-price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (FCFA) <span class="text-red-500">*</span></label>
                                    <input type="number" id="edit-price" wire:model.live="price" step="0.01" min="0"
                                           class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div>
                                    <label for="edit-capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacité <span class="text-red-500">*</span></label>
                                    <input type="number" id="edit-capacity" wire:model.live="capacity" min="1"
                                           class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="edit-image_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL de l'image (optionnel)</label>
                                <input type="url" id="edit-image_url" wire:model.live="image_url" placeholder="https://example.com/image.jpg"
                                       class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['image_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['image_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="mb-6">
                                <label for="edit-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (optionnel)</label>
                                <textarea id="edit-description" wire:model.live="description" rows="4"
                                          class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="flex justify-between items-center mt-6">
                                
                                <!--[if BLOCK]><![endif]--><?php if($event->matricule): ?>
                                    <div class="flex flex-col items-center p-2 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">QR Code Matricule</p>
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo e($event->matricule); ?>" alt="QR Code Matricule" class="w-24 h-24 sm:w-32 sm:h-32 rounded-md shadow-md">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">ID: <?php echo e($event->matricule); ?></p>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <button type="submit"
                                        class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-10">
                            <p class="text-lg text-gray-600 dark:text-gray-400">Vous n'avez pas la permission de modifier cet événement.</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div x-show="tab === 'status'">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Statut Actuel:
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            <?php if($status === 'active'): ?> bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-100
                            <?php elseif($status === 'cancelled'): ?> bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-100
                            <?php else: ?> bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 <?php endif; ?>">
                            <?php echo e(ucfirst($status)); ?>

                        </span>
                    </h4>

                    
                    <!--[if BLOCK]><![endif]--><?php if($showRefundPrompt): ?>
                        <div class="bg-yellow-100 dark:bg-yellow-800 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-100 px-4 py-3 rounded relative shadow-md mb-6" role="alert">
                            <strong class="font-bold">Attention!</strong>
                            <span class="block sm:inline">Cet événement a <span class="font-bold"><?php echo e($unrefundedTicketsCount); ?></span> ticket(s) payé(s) non remboursé(s).</span>
                            <p class="mt-2 text-sm">Veuillez choisir une action :</p>
                            <!--[if BLOCK]><![endif]--><?php if($canMakeRefund): ?>
                                <div class="mt-3">
                                    <label for="refundReason" class="block text-sm font-medium text-yellow-700 dark:text-yellow-100 mb-1">Raison du remboursement</label>
                                    <textarea id="refundReason" wire:model.live="refundReason" rows="2"
                                              class="w-full p-2 rounded-lg border border-yellow-300 dark:border-yellow-600 bg-yellow-50 dark:bg-yellow-700 text-yellow-900 dark:text-yellow-100 focus:ring-yellow-500 focus:border-yellow-500 <?php $__errorArgs = ['refundReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['refundReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-4">
                                    <button wire:click="makeRefund"
                                            class="w-full sm:w-auto px-4 py-2 rounded-lg bg-yellow-600 text-white font-semibold hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200"
                                            onclick="return confirm('Confirmez-vous l\'initiation des remboursements pour tous les tickets non remboursés ? Cette action est irréversible (selon l\'implémentation).')">
                                        Initier le Remboursement des Tickets
                                    </button>
                                    <button wire:click="confirmCancellationAfterRefundPrompt"
                                            class="w-full sm:w-auto px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200"
                                            onclick="return confirm('Confirmez-vous l\'annulation de l\'événement SANS remboursement préalable des tickets non remboursés ?')">
                                        Continuer l'annulation sans remboursement
                                    </button>
                                </div>
                            <?php else: ?>
                                <p class="mt-2 text-sm font-semibold">Vous n'avez pas la permission de gérer les remboursements. Veuillez contacter un administrateur.</p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


                    <div class="mb-6">
                        <label for="statusChangeReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Raison du changement de statut <span class="text-red-500">*</span></label>
                        <textarea id="statusChangeReason" wire:model.live="statusChangeReason" rows="3"
                                  class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['statusChangeReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="Expliquez la raison de l'annulation ou de la restauration."></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['statusChangeReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="flex space-x-4">
                        <!--[if BLOCK]><![endif]--><?php if($status === 'active' && $canCancelEvents): ?>
                            <button wire:click="cancelEvent"
                                    class="px-5 py-2.5 rounded-lg bg-red-600 text-white font-semibold shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200"
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cet événement ?')">
                                Annuler l'événement
                            </button>
                        <?php elseif($status === 'cancelled' && $canRestoreEvents): ?>
                            <button wire:click="restoreEvent"
                                    class="px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200"
                                    onclick="return confirm('Êtes-vous sûr de vouloir restaurer cet événement ?')">
                                Restaurer l'événement
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="mt-8 flex justify-end">
                        
                        <button onclick="window.open('<?php echo e(route('employee.events.generate-poster-pdf', $event->id)); ?>', '_blank')"
                                class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                            Imprimer l'Affiche PDF
                        </button>
                    </div>
                </div>

                
                <div x-show="tab === 'history'">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Historique des Statuts</h4>
                    <!--[if BLOCK]><![endif]--><?php if(empty($statusHistory)): ?>
                        <p class="text-gray-600 dark:text-gray-400">Aucun historique de statut pour cet événement.</p>
                    <?php else: ?>
                        <div class="space-y-4 mb-8">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                        <span class="font-semibold"><?php echo e(ucfirst($entry['status_type'])); ?></span>
                                        de '<span class="font-medium text-red-500"><?php echo e($entry['old_status'] ?? 'N/A'); ?></span>'
                                        à '<span class="font-medium text-green-500"><?php echo e($entry['new_status']); ?></span>'
                                        le <?php echo e(Carbon::parse($entry['created_at'])->format('d M Y à H:i')); ?>

                                        par <?php echo e($entry['changer_admin']['name'] ?? $entry['changer_employee']['name'] ?? $entry['changed_by_type']); ?>.
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Raison: "<?php echo e($entry['reason']); ?>"</p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 mt-8">Historique des Modifications de Champs</h4>
                    <!--[if BLOCK]><![endif]--><?php if(empty($activityLog)): ?>
                        <p class="text-gray-600 dark:text-gray-400">Aucune modification de champ enregistrée pour cet événement.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $activityLog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                        Champ '<span class="font-semibold text-blue-500"><?php echo e($entry['field_name']); ?></span>' modifié
                                        de '<span class="font-medium text-orange-500"><?php echo e($entry['old_value'] ?? 'vide'); ?></span>'
                                        à '<span class="font-medium text-green-500"><?php echo e($entry['new_value'] ?? 'vide'); ?></span>'
                                        le <?php echo e(Carbon::parse($entry['created_at'])->format('d M Y à H:i')); ?>

                                        par <?php echo e($entry['changer_admin']['name'] ?? $entry['changer_employee']['name'] ?? $entry['changed_by_type']); ?>.
                                    </p>
                                    <!--[if BLOCK]><![endif]--><?php if($entry['reason']): ?>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Raison: "<?php echo e($entry['reason']); ?>"</p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]--> 
        </div>
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/employee/manage-events/edit-event.blade.php ENDPATH**/ ?>