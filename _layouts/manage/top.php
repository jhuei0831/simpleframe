<?php
    use DebugBar\StandardDebugBar;
    use Kerwin\Core\Support\Facades\Message;
    if (is_null(Kerwin\Core\Support\Facades\Session::get('USER_ID'))) {
        Message::flash('權限不足!', 'error')->redirect(APP_ADDRESS);
    }
    if (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) {
        $debugbar = new StandardDebugBar();
        $debugbarRenderer = $debugbar->getJavascriptRenderer(APP_URL.'vendor/maximebf/debugbar/src/Debugbar/Resources');
    }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <!-- webpack -->
    <script src="<?php echo APP_SRC?>dist/manage/bundle.js"></script>
    <?php include_once($root.'_partials/manage/meta.php'); ?>
    <?php include_once($root.'_partials/manage/css.php'); ?>
    <title><?php echo isset($pageTitle) ? $pageTitle.'-'.APP_NAME : APP_NAME?></title>
    <!-- debug bar -->
    <?php echo (IS_DEBUG === 'TRUE' && in_array($_SERVER["REMOTE_ADDR"], $except_ip_list)) ? $debugbarRenderer->renderHead() : '' ?>
    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo APP_IMG?>favicon.ico">
</head>
<body style="font-family: 'Noto Sans TC', sans-serif;">
    <div>
        <?php include_once($root.'_partials/manage/sidebar.php'); ?>
        <?php include_once($root.'_partials/manage/nav.php'); ?>
        <main class="flex-1 relative pb-8 z-0 overflow-y-auto">
