<?php
/**
 * @var $content - content that added to this layout-template
 */
?>
<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <?php \Core\View\View::callbackReplace(function() { return _assets(\Core\View\View::getAssets()); }); ?>

</head>
<body>
<div id="app">
    <?= \Core\View\View::render('templates/header', ['companyName' => 'SomeCompanyName']) ?>
    <?= $content ?>
    <?= \Core\View\View::render('templates/footer') ?>
</div>
</body>
</html>