<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="zh-TW">
<head>
    <?php include_once($root.'_partials/manage/meta.php'); ?>
    <?php include_once($root.'_partials/manage/css.php'); ?>
    <title><?=isset($page_title) ? $page_title : APP_NAME?></title>
</head>
<body>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
        <?php include_once($root.'_partials/manage/sidebar.php'); ?>
        <?php include_once($root.'_partials/manage/nav.php'); ?>
        <main class="h-full overflow-y-auto">
            <div class="container px-6 mx-auto grid">
