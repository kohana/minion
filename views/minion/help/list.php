<?php echo I18n::translate('Minion is a CLI tool for performing tasks.') ?>

<?php echo I18n::translate('Usage') ?>:

    php index.php --task=<task> [--option=<value>] [--option2=<value2>]

<?php echo I18n::translate('Where {task} is one of the following') ?>:

<?php foreach($tasks as $task): ?>
    <?php echo $task.PHP_EOL ?>
<?php endforeach ?>

<?php echo I18n::translate('For more information on what a task does and usage details execute') ?>:

    php index.php --task=<task> --help
