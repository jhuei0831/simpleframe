<?php
    use DebugBar\StandardDebugBar;
    if (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) {
        $debugbar = new StandardDebugBar();
        $debugbarRenderer = $debugbar->getJavascriptRenderer(APP_URL.'vendor/maximebf/debugbar/src/Debugbar/Resources');
    }
?>
<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" lang="zh-TW">
<head>
    <?php include_once($root.'_partials/reception/meta.php'); ?>
    <?php include_once($root.'_partials/reception/css.php'); ?>
    <title><?php echo isset($page_title) ? $page_title : APP_NAME?></title>
    <!-- debug bar -->
    <?php echo (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) ? $debugbarRenderer->renderHead() : '' ?>
    <!-- webpack -->
    <script src="<?php echo APP_SRC?>dist/bundle.js"></script>
    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo APP_IMG?>favicon.ico">
</head>
<body style="font-family: 'Noto Sans TC', sans-serif;">
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
        <?php include_once($root.'_partials/reception/nav.php'); ?>
        <main class="h-full overflow-y-auto">
            <div class="container px-6 mx-auto">
