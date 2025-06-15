<?php $__env->startSection('title','MANAGE EMPLOYEES'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-8">
        
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Gestion des Rôles et Permissions</h2>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('gestion-roles.manage-roles-permissions', ['guardName' => 'employee']);

$__html = app('livewire')->mount($__name, $__params, 'lw-586226455-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('gestion-roles.permission-stats', ['guardName' => 'employee']);

$__html = app('livewire')->mount($__name, $__params, 'lw-586226455-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Gestion des Employee</h2>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('organization.patron.manage-employees.list-employees');

$__html = app('livewire')->mount($__name, $__params, 'lw-586226455-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>
    </div>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('organization.patron.manage-employees.create-employee');

$__html = app('livewire')->mount($__name, $__params, 'lw-586226455-3', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('organization.patron.manage-employees.employee-profile-card');

$__html = app('livewire')->mount($__name, $__params, 'lw-586226455-4', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('organization.patron.connected-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/organization/patron/pages/manage-employees.blade.php ENDPATH**/ ?>