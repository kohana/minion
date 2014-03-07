<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php echo __('Usage') ?>:

    php <?php echo $_SERVER['argv'][0] ?> --task=<?php echo $task ?> [--option=value]

<?php echo __('Details') ?>:

<?php foreach($tags as $tag_name => $tag_content): ?>
    <?php echo ucfirst($tag_name).': '.$tag_content.PHP_EOL ?>
<?php endforeach ?>

<?php echo __('Description') ?>:

    <?php echo $description ?>
