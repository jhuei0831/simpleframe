<?php
    $root = '../../';

    include $root . 'config/settings.php';

    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Permission;
    use Kerwin\Core\Support\Toolbox;

    if (!Permission::can('users-list')) {
        include_once $root . '_error/404.php';
        exit;
    }
    
    $roles = Database::table('roles')->get();
    $jsonRoles = json_encode($roles);
    Message::showFlash();
    include $root . '_layouts/manage/top.php';
?>
<!-- breadcrumb -->
<?php echo Toolbox::breadcrumb(APP_ADDRESS . 'manage', ['使用者管理' => '#']) ?>

<div class="container px-6 mx-auto grid" x-data="filter()">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">使用者管理</h2>
    <div class="flex justify-start mb-2">
        <a href="./create.php" class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-cyan-600 border border-transparent rounded-md active:bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500"><i class="bi bi-person-plus "></i> 新增</a>
        <a href="#" @click="toggleFilter()" class="px-3 py-1 ml-2 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-md active:bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"><i class="bi bi-filter "></i> 篩選</a>
    </div>
    <div id="filter" x-show="open">
        <form name="filterForm" id="filterForm" method="post" @submit="preventSubmit()">
            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">名稱</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">電子信箱</label>
                    <div class="mt-1">
                        <input type="text" name="email" id="email" autocomplete="postal-code" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label for="role" class="block text-sm font-medium text-gray-700">角色</label>
                    <div class="mt-1">
                        <select id="role" name="role" autocomplete="role" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="">請選擇</option>
                            <?php foreach ($roles as $key => $role): ?>
                                <option value="<?php echo $role->id?>"><?php echo $role->name?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label for="id" class="block text-sm font-medium text-gray-700">ID</label>
                    <div class="mt-1">
                        <input type="text" name="id" id="id" autocomplete="postal-code" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
            </div>
        </form>
        <div class="pt-5">
            <div class="flex justify-end">
                <button @click="resetFilter()" id="btnReset" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    重設
                </button>
                <button id="btnQuery" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    查詢
                </button>
            </div>
        </div> 
    </div>
    
    <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
        <div class="w-full lg:overflow-x-hidden overflow-x-scroll shadow">
        
            <table id="table" class="whitespace-nowrap row-border hover table-auto">
                <thead>
                    <tr
                        class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                        <th class="px-4 py-3">名稱</th>
                        <th class="px-4 py-3">電子信箱</th>
                        <th class="px-4 py-3">角色</th>
                        <th class="px-4 py-3">建立時間</th>
                        <th class="px-4 py-3">功能</th>
                        <th class="px-4 py-3 hidden">id</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    let roles = <?php echo $jsonRoles; ?>;
    let url = "ajax_users.php";
    let columns = [
        {
            "data": "name"
        },
        {
            "data": "email"
        },
        {
            "data": "role",
            "render": function(data) {
                roles.forEach(function(role) {
                    if (role.id === data) {
                        data = role.name;
                    }
                });
                return data;
            }
        },
        {
            "data": "created_at"
        },
        {
            "data": null,
            "orderable": false,
            "defaultContent":
            '<div class="flex items-center space-x-4 text-sm">'+
                '<i tabindex="0" role="link" class="bi bi-pencil-fill edit flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-300" style="cursor:pointer"></i>'+
                '<i tabindex="0" role="link" class="bi bi-trash-fill delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-300" style="cursor:pointer"></i>'+
            '</div>'
        },
        {
            "data": "id",
            "visible": false,
        },
    ];

    let table = $('#table').DataTable({
        "language": {
            "sProcessing":   "處理中...",
            "sLengthMenu":   "顯示 _MENU_ 項結果",
            "sZeroRecords":  "沒有匹配結果",
            "sInfo":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
            "sInfoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
            "sInfoFiltered": "(從 _MAX_ 項結果過濾)",
            "sInfoPostFix":  "",
            "sSearch":       "搜索:",
            "sUrl":          "",
            "oPaginate": {
                "sFirst":    "首頁",
                "sPrevious": "上頁",
                "sNext":     "下頁",
                "sLast":     "尾頁"
            }
        },
        'sDom': 'lrtip',
        "bProcessing": false,
        "serverSide": true,
        "ajax":{
            url: url,
            type: "post",
            data: function(d) {
                d.columns[0]['search']['value'] = $("#name").val();
                d.columns[1]['search']['value'] = $("#email").val();
                d.columns[2]['search']['value'] = $('#role option:selected').val();
                d.columns[5]['search']['value'] = $('#id').val();
            },
            error: function(res){
                console.log(res)
            },
        },
        "columns": columns
    });
</script>
<?php include $root . '_layouts/manage/bottom.php'?>

