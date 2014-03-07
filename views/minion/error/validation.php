<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php echo __('Parameter errors') ?>:

<?php foreach ($errors as $parameter => $error): ?>
    <?php echo $parameter.' - '.$error ?> 
<?php endforeach ?>

<?php echo __('For more help, run') ?>:

    php <?php echo $_SERVER['argv'][0] ?> --task=<?php echo $task ?> --help
