<?php
    use DebugBar\StandardDebugBar;
    use _models\framework\Message as MG;
    if (empty($_SESSION['USER_ID'])) {
        MG::flash('Permission Denied!', 'error');
        MG::redirect(APP_ADDRESS);
    }
    if (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) {
        $debugbar = new StandardDebugBar();
        $debugbarRenderer = $debugbar->getJavascriptRenderer(APP_URL.'vendor/maximebf/debugbar/src/Debugbar/Resources');
    }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <?php include_once($root.'_partials/manage/meta.php'); ?>
    <?php include_once($root.'_partials/manage/css.php'); ?>
    <title><?php echo isset($page_title) ? $page_title : APP_NAME?></title>
    <!-- debug bar -->
    <?php echo (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) ? $debugbarRenderer->renderHead() : '' ?>
    <!-- webpack -->
    <script src="<?php echo APP_SRC?>dist/bundle.js" defer></script>
    <!-- font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=RocknRoll+One&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'RocknRoll One', sans-serif;">
    <div>
        <?php include_once($root.'_partials/manage/sidebar.php'); ?>
        <?php include_once($root.'_partials/manage/nav.php'); ?>
        <main class="flex-1 relative pb-8 z-0 overflow-y-auto">
