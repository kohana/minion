<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php echo __('Parameter errors') ?>:

<?php foreach ($errors as $parameter => $error): ?>
    <?php echo $parameter.' - '.$error.PHP_EOL ?> 
<?php endforeach ?>

<?php echo __('For more help, run') ?>:

    php index.php --task=<?php echo $task ?> --help
