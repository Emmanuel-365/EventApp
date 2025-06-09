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

    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6 sm:p-8 w-full max-w-3xl relative"
         @click.stop>


        <button type="button" @click="$wire.close()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-8 text-center">
            Créer une Nouvelle Organisation
        </h2>

        <!--[if BLOCK]><![endif]--><?php if($errors->has('general')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Erreur !</strong>
                <span class="block sm:inline"><?php echo e($errors->first('general')); ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="errorMessage = null">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Fermer</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <form wire:submit.prevent="createOrganization" class="space-y-6">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nom de l'organisation:
                </label>
                <input type="text" id="nom" wire:model.lazy="nom" required
                       class="form-input block w-full rounded-md border-gray-300 shadow-sm
                              dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                              focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50
                              transition ease-in-out duration-150 p-2.5"
                       placeholder="Ex: Mon Entreprise SARL">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div>
                <label for="NIU" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Numéro d'Identification Unique (NIU):
                </label>
                <input type="text" id="NIU" wire:model.lazy="NIU" required
                       class="form-input block w-full rounded-md border-gray-300 shadow-sm
                              dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                              focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50
                              transition ease-in-out duration-150 p-2.5"
                       placeholder="Ex: M1234567890N">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['NIU'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Type d'entreprise:
                </label>
                <select id="type" wire:model="type" required
                        class="form-select block w-full rounded-md border-gray-300 shadow-sm
                               dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                               focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50
                               transition ease-in-out duration-150 p-2.5">
                    <option value="">Sélectionnez un type</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $typesEntreprise; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($typeOption->value); ?>"><?php echo e($typeOption->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div>
                <label for="date_creation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Date de création:
                </label>
                <input type="date" id="date_creation" wire:model="date_creation" required
                       class="form-input block w-full rounded-md border-gray-300 shadow-sm
                              dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                              focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50
                              transition ease-in-out duration-150 p-2.5">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_creation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div>
                <label for="subdomain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Sous-domaine:
                </label>
                <div class="flex rounded-md shadow-sm">
                    <input type="text" id="subdomain" wire:model.lazy="subdomain" required
                           class="flex-1 block w-full min-w-0 rounded-l-md border-gray-300 shadow-sm
                                  dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200
                                  focus:border-blue-500 focus:ring focus:ring-500 focus:ring-opacity-50
                                  transition ease-in-out duration-150 p-2.5"
                           placeholder="Ex: monorganisation">
                    <span class="inline-flex items-center px-4 rounded-r-md border border-l-0 border-gray-300
                                 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-300 text-base
                                 font-mono">
                        .<?php echo e(parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost'); ?>

                    </span>
                </div>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['subdomain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="pt-4">
                <button type="submit"
                        class="w-full inline-flex justify-center py-3 px-6 border border-transparent rounded-md
                               shadow-sm text-lg font-semibold text-white bg-blue-600
                               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2
                               focus:ring-blue-500 transition ease-in-out duration-150
                               dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-600">
                    Créer Organisation
                </button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/organization/organizer/manage-organizations/create-organization.blade.php ENDPATH**/ ?>