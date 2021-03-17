<?php
    $root = '../../';

    use _models\Message as MG;

    include($root.'_config/settings.php');
    include($root.'_layouts/manage/top.php');
    MG::show_flash();
?>    
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Roles</h2>
    <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table id="table" class="w-full whitespace-no-wrap row-border hover">
                <thead>
                    <tr
                        class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Created At</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php include($root.'_layouts/manage/bottom.php') ?>
<script type="text/javascript">
    let url = "ajax_roles.php";
    let columns = [ 
        {
            "data": "id"
        },
        {
            "data": "name"
        },
        {
            "data": "created_at"
        },
        {
            "data": null,
            "defaultContent": 
            '<div class="flex items-center space-x-4 text-sm">'+ 
                '<i class="bi bi-pencil-fill edit flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gra" style="cursor:pointer"></i>'+
                '<i class="bi bi-trash-fill delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gra" style="cursor:pointer"></i>'+
            '</div>'
        },
    ];
</script>
<!-- 刪除跟修改按鈕 -->
<script src="<?=APP_JS?>datatable.js"></script>