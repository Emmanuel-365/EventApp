<div x-data="{ show: <?php if ((object) ('showDetailsModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDetailsModal'->value()); ?>')<?php echo e('showDetailsModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDetailsModal'); ?>')<?php endif; ?>.live }"
     x-show="show"
     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none;">

    
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="show = false"></div>

    
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl leading-6 font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                            Détails de l'événement : <?php echo e($event->title ?? 'Chargement...'); ?>

                        </h3>
                        <!--[if BLOCK]><![endif]--><?php if(Auth::guard('employee')->user()?->can('update-events') && $activeTab === 1 && $event->exists): ?>
                            <!--[if BLOCK]><![endif]--><?php if(!$editing): ?>
                                <button wire:click="toggleEditMode"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800 transition duration-150">
                                    <i class="fas fa-edit mr-2"></i> Modifier
                                </button>
                            <?php else: ?>
                                <div class="flex space-x-2">
                                    <button wire:click="saveChanges"
                                            wire:loading.attr="disabled"
                                            wire:target="saveChanges, newImage"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 dark:focus:ring-offset-gray-800 transition duration-150">
                                        <i class="fas fa-save mr-2"></i> Enregistrer
                                    </button>
                                    <button wire:click="toggleEditMode"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                        <i class="fas fa-times mr-2"></i> Annuler
                                    </button>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>


                    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button wire:click="selectTab(1)"
                                    class="<?php echo e($activeTab === 1 ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-500'); ?>

                                           whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150">
                                Détails
                            </button>
                            <button wire:click="selectTab(2)"
                                    class="<?php echo e($activeTab === 2 ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-500'); ?>

                                           whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150">
                                Participants (<?php echo e($totalTicketsSold); ?>)
                            </button>
                            <button wire:click="selectTab(3)"
                                    class="<?php echo e($activeTab === 3 ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-500'); ?>

                                           whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150">
                                Activités
                            </button>
                            <button wire:click="selectTab(4)"
                                    class="<?php echo e($activeTab === 4 ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-500'); ?>

                                           whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150">
                                Historique Statuts
                            </button>
                        </nav>
                    </div>

                    
                    <div class="max-h-[70vh] overflow-y-auto pr-2 pb-4">
                        <!--[if BLOCK]><![endif]--><?php if($event->exists): ?> 
                        <div>
                            
                            <!--[if BLOCK]><![endif]--><?php if($activeTab === 1): ?>
                                <div class="space-y-4 text-gray-700 dark:text-gray-300">
                                    
                                    <div class="mb-4 text-center">
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            
                                            <!--[if BLOCK]><![endif]--><?php if($newImage): ?>
                                                <img src="<?php echo e($newImage->temporaryUrl()); ?>" alt="Nouvelle image de l'événement" class="max-h-60 mx-auto rounded-lg shadow-md object-cover border-2 border-dashed border-blue-400">
                                            <?php elseif($currentImageUrl): ?>
                                                <img src="<?php echo e($currentImageUrl); ?>" alt="Image actuelle de l'événement" class="max-h-60 mx-auto rounded-lg shadow-md object-cover">
                                            <?php else: ?>
                                                <div class="max-h-60 mx-auto rounded-lg shadow-md object-cover bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xl font-semibold" style="height: 15rem; width: 100%;">
                                                    Pas d'image
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            
                                            <div class="mt-4">
                                                <label for="newImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Changer l'image</label>
                                                <input type="file" id="newImage" wire:model.live="newImage"
                                                       class="block w-full text-sm text-gray-900 dark:text-gray-100
                                                                  file:mr-4 file:py-2 file:px-4
                                                                  file:rounded-full file:border-0
                                                                  file:text-sm file:font-semibold
                                                                  file:bg-blue-50 file:text-blue-700
                                                                  hover:file:bg-blue-100
                                                                  dark:file:bg-blue-800 dark:file:text-blue-100 dark:hover:file:bg-blue-700">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newImage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                <div wire:loading wire:target="newImage" class="text-blue-500 text-sm mt-2">Téléchargement de l'image...</div>
                                            </div>
                                        <?php else: ?>
                                            <!--[if BLOCK]><![endif]--><?php if($event->image_url): ?>
                                                <img src="<?php echo e($event->image_url); ?>" alt="Image de l'événement" class="max-h-60 mx-auto rounded-lg shadow-md object-cover">
                                            <?php else: ?>
                                                <div class="max-h-60 mx-auto rounded-lg shadow-md object-cover bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xl font-semibold" style="height: 15rem; width: 100%;">
                                                    Pas d'image
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <p><strong>Matricule:</strong> <?php echo e($event->matricule ?? 'N/A'); ?></p>

                                    
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titre:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="text" id="title" wire:model.live="title"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e($event->title ?? 'N/A'); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <textarea id="description" wire:model.live="description" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                                 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                                 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e($event->description ?? 'Aucune description.'); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="date" id="date" wire:model.live="date"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e($event->date ? \Carbon\Carbon::parse($event->date)->format('d/m/Y') : 'N/A'); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Heure:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="time" id="time" wire:model.live="time"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e($event->time ? \Carbon\Carbon::parse($event->time)->format('H:i') : 'N/A'); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lieu:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="text" id="location" wire:model.live="location"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e($event->location ?? 'N/A'); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="number" step="any" id="latitude" wire:model.live="latitude"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e($event->latitude ?? 'N/A'); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="number" step="any" id="longitude" wire:model.live="longitude"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e($event->longitude ?? 'N/A'); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix (FCFA):</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="number" step="0.01" id="price" wire:model.live="price"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e(number_format($event->price, 0, ',', '.')); ?> FCFA</p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <div>
                                        <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacité:</label>
                                        <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                            <input type="number" id="capacity" wire:model.live="capacity"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                                              dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100
                                                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <p class="mt-1 text-base"><?php echo e(number_format($event->capacity, 0, ',', '.')); ?></p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    
                                    <p><strong>Billets disponibles:</strong> <?php echo e(number_format($event->available_tickets, 0, ',', '.')); ?></p>

                                    
                                    <p><strong>Statut:</strong> <span class="capitalize px-2 py-1 rounded-full text-xs font-semibold
                                            <?php if($event->status == 'published'): ?> bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                            <?php elseif($event->status == 'not_published'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                            <?php elseif($event->status == 'cancelled'): ?> bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100
                                            <?php else: ?> bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 <?php endif; ?>">
                                            <?php echo e(str_replace('_', ' ', $event->status)); ?>

                                        </span></p>

                                    <p><strong>Créé le:</strong> <?php echo e($event->created_at?->format('d/m/Y H:i') ?? 'N/A'); ?></p>
                                    <p><strong>Dernière mise à jour:</strong> <?php echo e($event->updated_at?->format('d/m/Y H:i') ?? 'N/A'); ?></p>
                                    <!--[if BLOCK]><![endif]--><?php if($event->status == 'cancelled' && $event->cancelled_reason): ?>
                                        <p class="text-red-500"><strong>Annulé pour:</strong> <?php echo e($event->cancelled_reason); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <hr class="border-gray-200 dark:border-gray-700 my-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Statistiques Rapides</h4>
                                    <p><strong>Total Billets Vendus:</strong> <?php echo e(number_format($totalTicketsSold, 0, ',', '.')); ?></p>
                                    <p><strong>Revenu Total Estimé:</strong> <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?> FCFA</p>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($activeTab === 2): ?>
                                <div>
                                    <!--[if BLOCK]><![endif]--><?php if($participants->isEmpty()): ?>
                                        <p class="text-gray-700 dark:text-gray-300">Aucun participant pour cet événement pour le moment.</p>
                                    <?php else: ?>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Nom Participant
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Email
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Matricule Billet
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Date Achat
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Statut Billet
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            <?php echo e($ticket->client->name ?? 'Client Inconnu'); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($ticket->client->email ?? 'N/A'); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($ticket->ticket_code); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : 'N/A'); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                                    <span class="capitalize px-2 py-1 rounded-full text-xs font-semibold
                                                                        <?php if($ticket->status == 'paid'): ?> bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                                                        <?php elseif($ticket->status == 'pending'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                                                        <?php elseif($ticket->status == 'cancelled'): ?> bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100
                                                                        <?php else: ?> bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 <?php endif; ?>">
                                                                        <?php echo e(str_replace('_', ' ', $ticket->status)); ?>

                                                                    </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($activeTab === 3): ?>
                                <div>
                                    <!--[if BLOCK]><![endif]--><?php if($activities->isEmpty()): ?>
                                        <p class="text-gray-700 dark:text-gray-300">Aucune activité enregistrée pour cet événement.</p>
                                    <?php else: ?>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Date
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Champ
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Ancienne Valeur
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Nouvelle Valeur
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Par
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Raison
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            <?php echo e($activity->created_at ? $activity->created_at->format('d/m/Y H:i') : 'N/A'); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($activity->field_name); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($activity->old_value ?? 'N/A'); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($activity->new_value ?? 'N/A'); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <!--[if BLOCK]><![endif]--><?php if($activity->changed_by_type === 'admin'): ?>
                                                                Admin (Central)
                                                            <?php elseif($activity->changed_by_type === 'employee'): ?>
                                                                <?php echo e($activity->changerEmployee->name ?? 'Employé Inconnu'); ?> (Employé)
                                                            <?php else: ?>
                                                                Inconnu
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($activity->reason ?? 'N/A'); ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($activeTab === 4): ?>
                                <div>
                                    <!--[if BLOCK]><![endif]--><?php if($statusHistories->isEmpty()): ?>
                                        <p class="text-gray-700 dark:text-gray-300">Aucun historique de statut enregistré pour cet événement.</p>
                                    <?php else: ?>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Date
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Type de Changement
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Ancien Statut
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Nouveau Statut
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Par
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Raison
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            <?php echo e($history->created_at ? $history->created_at->format('d/m/Y H:i') : 'N/A'); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($history->status_type); ?>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                                    <span class="capitalize px-2 py-1 rounded-full text-xs font-semibold
                                                                        <?php if($history->old_status == 'published'): ?> bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                                                        <?php elseif($history->old_status == 'not_published'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                                                        <?php elseif($history->old_status == 'cancelled'): ?> bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100
                                                                        <?php else: ?> bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 <?php endif; ?>">
                                                                        <?php echo e(str_replace('_', ' ', $history->old_status) ?? 'N/A'); ?>

                                                                    </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                                    <span class="capitalize px-2 py-1 rounded-full text-xs font-semibold
                                                                        <?php if($history->new_status == 'published'): ?> bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                                                        <?php elseif($history->new_status == 'not_published'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                                                        <?php elseif($history->new_status == 'cancelled'): ?> bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100
                                                                        <?php else: ?> bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 <?php endif; ?>">
                                                                        <?php echo e(str_replace('_', ' ', $history->new_status)); ?>

                                                                    </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <!--[if BLOCK]><![endif]--><?php if($history->changed_by_type === 'admin'): ?>
                                                                Admin (Central)
                                                            <?php elseif($history->changed_by_type === 'employee'): ?>
                                                                <?php echo e($history->changerEmployee->name ?? 'Employé Inconnu'); ?> (Employé)
                                                            <?php else: ?>
                                                                Inconnu
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                            <?php echo e($history->reason ?? 'N/A'); ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <?php else: ?>
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                Chargement des détails de l'événement...
                            </p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="button" wire:click="closeModal"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-6 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm
                           transition duration-150">
                <i class="fas fa-times mr-2"></i>
                Fermer
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/employee/manage-events/view-event-details.blade.php ENDPATH**/ ?>