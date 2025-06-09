<?php $__env->startSection('title', 'MES ORGANISATIONS'); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        
        <div class="mb-6 flex justify-end">
            <button type="button"
                    @click="$dispatch('openCreateOrganizationModal')" 
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-600 transition-colors duration-200">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                Créer Organisation
            </button>
        </div>

        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('organization.organizer.manage-organizations.organizations-list');

$__html = app('livewire')->mount($__name, $__params, 'lw-988992564-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('organization.organizer.manage-organizations.create-organization');

$__html = app('livewire')->mount($__name, $__params, 'lw-988992564-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('organization.organizer.manage-organizations.organization-details-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-988992564-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('organization.organizer.connected-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/organization/organizer/pages/organizations.blade.php ENDPATH**/ ?>