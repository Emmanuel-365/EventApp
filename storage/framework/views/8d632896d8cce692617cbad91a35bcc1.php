<?php $__env->startSection('title',"MANAGE ORGANIZER'S ORGANISATIONS"); ?>

<?php $__env->startSection('content'); ?>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.manage-organizations.admin-organizations-list' , ['organizer' => $organizer]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1784291003-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.connected-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MARCAU\PhpstormProjects\EventApp\resources\views/admin/pages/manage-organizer-organizations.blade.php ENDPATH**/ ?>