<?php echo __('Usage') ?>:

    php index.php --task=<?php echo $task ?> [--option=<value>] [--option2=<value2>]

<?php echo __('Details') ?>:

<?php foreach($tags as $tag_name => $tag_content): ?>
    <?php echo ucfirst($tag_name).': '.$tag_content.PHP_EOL ?>
<?php endforeach ?>

<?php echo __('Description') ?>:

    <?php echo $description ?>
