<div x-data="{ show: <?php if ((object) ('showCreateModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showCreateModal'->value()); ?>')<?php echo e('showCreateModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showCreateModal'); ?>')<?php endif; ?>.live }"
     x-show="show"
     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 overlay"
     style="display: none;">

    
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="showCreateModal = false"></div>

    
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-2xl leading-6 font-bold text-gray-900 dark:text-gray-100 mb-6" id="modal-title">
                        Créer un Nouvel Événement
                    </h3>
                    
                    <div class="max-h-[60vh] overflow-y-auto pr-2 pb-4"> 
                        <form  enctype="multipart/form-data" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titre de l'événement</label>
                                    <input type="text" id="title" wire:model="title"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                  focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                    <input type="date" id="date" wire:model="date"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                  focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Heure</label>
                                    <input type="time" id="time" wire:model="time"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                  focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (FCFA)</label>
                                    <input type="number" id="price" wire:model="price" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                  focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacité</label>
                                    <input type="number" id="capacity" wire:model="capacity"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                  focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div class="col-span-full">
                                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lieu de l'événement (adresse texte)</label>
                                    <input type="text" id="location" wire:model="location"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                  focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                           placeholder="Ex: Palais des Congrès, Yaoundé">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="image_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image de l'événement</label>
                                    <input type="file" id="image_file" wire:model="image_file"
                                           class="w-full text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4
                                                  file:rounded-full file:border-0 file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  dark:file:bg-blue-900 dark:file:text-blue-200 dark:hover:file:bg-blue-800">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['image_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($image_file): ?>
                                        <div class="mt-2 flex items-center space-x-2">
                                            <p class="text-xs text-gray-500">Prévisualisation :</p>
                                            <img src="<?php echo e($image_file->temporaryUrl()); ?>" class="h-20 w-20 object-cover rounded-lg shadow">
                                            <button type="button" wire:click="clearImage" class="text-red-500 hover:text-red-700 text-sm">
                                                <i class="fas fa-times-circle"></i> Supprimer
                                            </button>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea id="description" wire:model="description" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                 focus:ring-blue-500 focus:border-blue-500 transition duration-150"></textarea>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg">
            <button type="button" wire:click="store"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm
               dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800 transition duration-150"
                    wire:loading.attr="disabled" wire:target="store">
                <i class="fas fa-save mr-2"></i>
                Créer l'événement
            </button>
            <button type="button" wire:click="closeModal"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-6 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm
                           transition duration-150">
                <i class="fas fa-times mr-2"></i>
                Annuler
            </button>
        </div>
    </div>

    
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/employee/manage-events/create-event.blade.php ENDPATH**/ ?>