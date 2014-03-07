<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php echo __('Minion is a CLI tool for performing tasks.') ?>

<?php echo __('Usage') ?>:

    php index.php --task=<task> [--option=<value>] [--option2=<value2>]

<?php echo __('Where {task} is one of the following') ?>:

<?php foreach($tasks as $task): ?>
    <?php echo $task.PHP_EOL ?>
<?php endforeach ?>

<?php echo __('For more information on what a task does and usage details execute') ?>:

    php index.php --task=<task> --help
