<?php
    $root = '../';

    include($root.'_config/settings.php');

    use _models\framework\Message as MG;
    use _models\framework\Role;
    
    if (!Role::has('admin')) {
        include_once($root.'_error/404.php');
        exit;
    }
    
    MG::show_flash();
    include($root.'_layouts/manage/top.php');
?>    
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Manage</h2>
    <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            
        </div>
    </div>
</div>

<?php include($root.'_layouts/manage/bottom.php') ?>
