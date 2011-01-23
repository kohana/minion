<?php if ( ! $quiet): ?>
Executed <?php echo count($executed_migrations); ?> migrations

Current versions of locations:
<?php foreach ($location_versions as $location): ?>
* <?php echo $location['location'] ?> : <?php echo $location['timestamp'] ?> (<?php echo $location['description']; ?>)
<?php endforeach; ?>

<?php if ($dry_run): ?>
This was a dry run, if it was a real run the following SQL would've been executed:
<?php endif; ?>
<?php endif; ?>
<?php foreach ($dry_run_sql as $location => $migrations): ?>

<?php $location_padding = str_repeat('#', strlen($location)); ?>
##################<?php echo $location_padding ?>##
# Begin Location: <?php echo $location; ?> #
##################<?php echo $location_padding ?>##

<?php foreach ($migrations as $timestamp => $sql): ?>
# Begin <?php echo $timestamp; ?>

<?php foreach ($sql as $query): ?>

<?php echo $query;?>;
<?php endforeach; ?>

# End <?php echo $timestamp; ?>

<?php endforeach; ?>

################<?php echo $location_padding ?>##
# End Location: <?php echo $location; ?> #
################<?php echo $location_padding ?>##
<?php endforeach; ?>

