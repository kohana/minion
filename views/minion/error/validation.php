<?php echo I18n::translate('Parameter errors') ?>:

<?php foreach ($errors as $parameter => $error): ?>
    <?php echo $parameter.' - '.$error.PHP_EOL ?> 
<?php endforeach ?>

<?php echo I18n::translate('For more help, run') ?>:

    php index.php --task=<?php echo $task ?> --help
