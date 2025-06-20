<?php $__env->startSection('title', 'Gérer les Événements'); ?>

<?php $__env->startSection('content'); ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('employee.manage-events.list-events');

$__html = app('livewire')->mount($__name, $__params, 'lw-3887908568-0', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('employee.manage-events.view-event-details');

$__html = app('livewire')->mount($__name, $__params, 'lw-3887908568-1', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('employee.manage-events.create-event');

$__html = app('livewire')->mount($__name, $__params, 'lw-3887908568-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?> -
<?php $__env->stopSection(); ?>

<?php echo $__env->make('employee.connected-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/employee/pages/manage-events.blade.php ENDPATH**/ ?>