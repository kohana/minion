<?php if($dry_run): ?>
<?php if( ! $quiet): ?>

This was a dry run, SQL is as follows:
<?php endif; ?>

<?php foreach($dry_run_sql as $location => $migrations): ?>
<?php $location_padding = str_repeat('#', strlen($location)); ?>
##################<?php echo $location_padding ?>##
# Begin Location: <?php echo $location; ?> #
##################<?php echo $location_padding ?>##

<?php foreach($migrations as $timestamp => $sql): ?>
# Begin <?php echo $timestamp; ?>

<?php foreach($sql as $query): ?>

<?php echo $query;?>;
<?php endforeach; ?>

# End <?php echo $timestamp; ?>

<?php endforeach; ?>

################<?php echo $location_padding ?>##
# End Location: <?php echo $location; ?> #
################<?php echo $location_padding ?>##
<?php endforeach; ?>

<?php endif; ?>

