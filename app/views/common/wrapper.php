<?php
use system\classes\LinkBuilder;
use system\core\Cfg;
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0">
        <title><?= $page->getTitle() ?></title>
        <meta name="keywords" content="<?= $page->getKeywords() ?>" />
        <meta name="description" content="<?= $page->getDescription() ?>" />
        <?= $page->alternate ?>
        <?= $page->css ?>
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>       
    	<?= $page->js ?>
    </head>
    <body>
    <?php foreach (Cfg::$get->langs as $lang => $local): ?>
        <a href="<?= LinkBuilder::url(Cfg::$get->route->getController(), Cfg::$get->route->getAction(), ['lang' => $lang]) ?>"><?= $local ?></a>
    <?php endforeach; ?>

    <p><?= Lang::get('wrap') ?></p>

    <?= $content ?>

    </body>
</html>