<?php echo I18n::translate('Usage') ?>:

    php index.php --task=<?php echo $task ?> [--option=<value>] [--option2=<value2>]

<?php echo I18n::translate('Details') ?>:

<?php foreach($tags as $tag_name => $tag_content): ?>
    <?php echo ucfirst($tag_name).': '.$tag_content.PHP_EOL ?>
<?php endforeach ?>

<?php echo I18n::translate('Description') ?>:

    <?php echo $description ?>
