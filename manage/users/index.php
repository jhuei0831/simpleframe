<?php
    $root = '../../';

    include($root.'_config/settings.php');

    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Permission;
    use Kerwin\Core\Support\Toolbox;

    if (!Permission::can('users-list')) {
        include_once($root.'_error/404.php');
        exit;
    }
    
    Message::showFlash();
    include($root.'_layouts/manage/top.php');
?>    
<!-- breadcrumb -->
<?php echo Toolbox::breadcrumb(APP_ADDRESS.'manage', ['使用者管理'=> '#'])?>

<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">使用者管理</h2>
    <div class="flex justify-start mb-2">
        <a href="./create.php" class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-cyan-600 border border-transparent rounded-md active:bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:shadow-outline-cyan"><i class="bi bi-person-plus "></i> 新增</a>
    </div>
    <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
        <div class="w-full lg:overflow-x-hidden overflow-x-scroll shadow">
            <table id="table" class="whitespace-no-wrap row-border hover">
                <thead>
                    <tr
                        class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                        <th class="px-4 py-3">名稱</th>
                        <th class="px-4 py-3">電子信箱</th>
                        <th class="px-4 py-3">角色</th>
                        <th class="px-4 py-3">建立時間</th>
                        <th class="px-4 py-3">功能</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    let url = "ajax_users.php";
    let columns = [
        {
            "data": "name"
        },
        {
            "data": "email"
        },
        {
            "data": "role"
        },
        {
            "data": "created_at"
        },
        {
            "data": null,
            "orderable": false,
            "defaultContent": 
            '<div class="flex items-center space-x-4 text-sm">'+ 
                '<i class="bi bi-pencil-fill edit flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gra" style="cursor:pointer"></i>'+
                '<i class="bi bi-trash-fill delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gra" style="cursor:pointer"></i>'+
            '</div>'
        },
    ];
</script>
<?php include($root.'_layouts/manage/bottom.php') ?>

