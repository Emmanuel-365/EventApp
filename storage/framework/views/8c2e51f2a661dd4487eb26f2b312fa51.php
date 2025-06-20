<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md transition-colors duration-300 ease-in-out">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Gestion des Organisateurs</h1>

    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Succès !</strong>
            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php if(session()->has('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erreur !</strong>
            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php if(session()->has('message')): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Information :</strong>
            <span class="block sm:inline"><?php echo e(session('message')); ?></span>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


    <!--[if BLOCK]><![endif]--><?php if($canSeeOrganizers): ?>
        <div class="mb-6 flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 md:space-x-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par nom, email, matricule..."
                   class="flex-grow w-full md:w-auto px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500
                          bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                          shadow-sm transition-colors duration-300 ease-in-out">

            <select wire:model.live="perPage"
                    class="w-full md:w-auto px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500
                           bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm transition-colors duration-300 ease-in-out">
                <option value="5">5 par page</option>
                <option value="10">10 par page</option>
                <option value="25">25 par page</option>
                <option value="50">50 par page</option>
            </select>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th wire:click="sortBy('matricule')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer rounded-tl-lg">
                        Matricule
                        <!--[if BLOCK]><![endif]--><?php if($sortBy === 'matricule'): ?>
                            <span class="ml-1 text-xs">
                                    <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?> &uarr; <?php else: ?> &darr; <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th wire:click="sortBy('nom')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                        Nom
                        <!--[if BLOCK]><![endif]--><?php if($sortBy === 'nom'): ?>
                            <span class="ml-1 text-xs">
                                    <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?> &uarr; <?php else: ?> &darr; <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th wire:click="sortBy('email')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                        Email
                        <!--[if BLOCK]><![endif]--><?php if($sortBy === 'email'): ?>
                            <span class="ml-1 text-xs">
                                    <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?> &uarr; <?php else: ?> &darr; <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th wire:click="sortBy('profile_verification_status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                        Statut Profil
                        <!--[if BLOCK]><![endif]--><?php if($sortBy === 'profile_verification_status'): ?>
                            <span class="ml-1 text-xs">
                                    <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?> &uarr; <?php else: ?> &darr; <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Statut Ban
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider rounded-tr-lg">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $organizers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organizer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="organizer-<?php echo e($organizer->id); ?>" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            <?php echo e($organizer->matricule); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <?php echo e($organizer->nom); ?> <?php echo e($organizer->prenom); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <?php echo e($organizer->email); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php
                                $statusClass = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                    'validated' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                    'en attente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                    'validé' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                    'rejeté' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                ];
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($statusClass[$organizer->profile_verification_status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200'); ?>">
                                    <?php echo e(ucfirst($organizer->profile_verification_status)); ?>

                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php
                                $isBanned = app(\App\Services\BanService::class)->isUserBanned($organizer);
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($isBanned ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'); ?>">
                                    <?php echo e($isBanned ? 'Banni' : 'Actif'); ?>

                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <!--[if BLOCK]><![endif]--><?php if($canSeeOrganizers): ?> 
                            <button wire:click="selectOrganizer('<?php echo e($organizer->id); ?>')"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                                Détails
                            </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <a href="<?php echo e(route('admin.manageOrganizerOrganizationsView',['organizer' => $organizer->id ])); ?>"
                            class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200"
                               title="Voir les organisations de <?php echo e($organizer->nom); ?>">
                                Organisations
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                            Aucun organisateur trouvé.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <?php echo e($organizers->links()); ?>

        </div>
    <?php else: ?>
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
            <p>Vous n'avez pas la permission de voir la liste des organisateurs.</p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($showDetailModal): ?>
                
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.manage-organizers.organizer-details-modal', ['organizerId' => $selectedOrganizerId]);

$__html = app('livewire')->mount($__name, $__params, $selectedOrganizerId, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/livewire/admin/manage-organizers/organizers-data-table.blade.php ENDPATH**/ ?>