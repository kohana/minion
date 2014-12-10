<?php echo __('Usage') ?>:

    php index.php --task=<?php echo $task ?> <?php foreach ($options as $key => $value){echo "[--{$key}=<{$value}>]";}?>

<?php echo __('Details') ?>:

<?php foreach($tags as $tag_name => $tag_content): ?>
    <?php echo ucfirst($tag_name).': '.$tag_content.PHP_EOL ?>
<?php endforeach ?>

<?php echo __('Description') ?>:

    <?php echo $description.PHP_EOL ?>
