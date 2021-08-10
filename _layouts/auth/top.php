<?php
    use DebugBar\StandardDebugBar;
    if (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) {
        $debugbar = new StandardDebugBar();
        $debugbarRenderer = $debugbar->getJavascriptRenderer(APP_URL.'vendor/maximebf/debugbar/src/Debugbar/Resources');
    }
?>
<html lang="zh-TW">
    <head>
        <?php include_once($root.'_partials/reception/meta.php'); ?>
        <?php include_once($root.'_partials/reception/css.php'); ?>
        <title><?php echo isset($page_title) ? $page_title : APP_NAME?></title>
        <!-- debug bar -->
        <?php echo IS_DEBUG === 'TRUE' ? $debugbarRenderer->renderHead() : '' ?>
        <!-- webpack -->
        <script src="<?php echo APP_SRC?>dist/bundle.js" defer></script>
        <!-- font -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=RocknRoll+One&display=swap" rel="stylesheet">
    </head>
    <body style="font-family: 'RocknRoll One', sans-serif;">