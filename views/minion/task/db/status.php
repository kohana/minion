<?php foreach($locations as $location => $status): ?>
 * <?php echo $location ?> <?php echo ($status !== NULL ? $status : 'Not installed'); ?>

<?php endforeach; ?>
