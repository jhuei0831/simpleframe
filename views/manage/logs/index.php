<?php
    $root = '../../';

    include($root.'config/settings.php');

    use models\Variable;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Permission;

    if (!Permission::can('logs-list')) {
        include_once($root.'_error/404.php');
        exit;
    }

    Message::showFlash();
    include($root.'_layouts/manage/top.php');
?>    
<!-- breadcrumb -->
<?php echo Toolbox::breadcrumb(APP_ADDRESS.'manage', ['LOG清單'=> '#'])?>

<div class="container px-6 mx-auto grid" x-data="filter()">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">LOG清單</h2>
    <div class="flex justify-start mb-2">
        <a href="#" @click="toggleFilter()" class="px-3 py-1 ml-2 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-md active:bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"><i class="bi bi-filter "></i> 篩選</a>
    </div>
    <div id="filter" x-show="open">
        <form name="filterForm" id="filterForm" method="post" @submit="preventSubmit()">
            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">IP</label>
                    <div class="mt-1">
                        <input type="text" name="ip" id="ip" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">等級</label>
                    <div class="mt-1">
                        <input type="text" name="level" id="level" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label for="message" class="block text-sm font-medium text-gray-700">訊息</label>
                    <div class="mt-1">
                        <select id="message" name="message" autocomplete="message" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="">請選擇</option>
                            <?php foreach (Variable::$logMessages as $key => $message): ?>
                                <option value="<?php echo $message?>"><?php echo $message?></option>
                            <?php endforeach; ?>
                        </select>
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
            <table id="table" class="whitespace-nowrap row-border hover">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">IP</th>
                        <th class="px-4 py-3">等級</th>
                        <th class="px-4 py-3">訊息</th>
                        <th class="px-4 py-3">建立時間</th>
                        <th class="px-4 py-3">功能</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    // Datatable Variables
    let url = "ajax_logs.php";
    let columns = [
        {
            "data": "id"
        },
        {
            "data": "ip"
        },
        {
            "data": "level",
            "render": function(data) {
                switch (data) {
                    case '100':
                        return 'Debug';
                        break;
                    case '200':
                        return 'Info';
                        break;
                    case '250':
                        return 'Notice';
                        break;
                    case '300':
                        return 'Warning';
                        break;
                    case '400':
                        return 'Error';
                        break;
                    default:
                        return data;
                        break;
                }
                return data;
            }
        },
        {
            "data": "message"
        },
        {
            "data": "created_at"
        },
        {
            "data": null,
            "orderable": false,
            "defaultContent": 
            '<div class="flex items-center space-x-4 text-sm">'+ 
                '<i tabindex="0" role="link" class="bi bi-zoom-in detail flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-300" style="cursor:pointer"></i>'+
            '</div>'
        }
    ];

    // Datatable
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
                d.columns[1]['search']['value'] = $("#ip").val();
                d.columns[2]['search']['value'] = $("#level").val();
                d.columns[3]['search']['value'] = $("#message").val();
            },
            error: function(res){
                console.log(res)
            },
        },
        "columns": columns
    });

    // 詳細資料按鈕導向
    $('#table tbody').on('click keypress', '.detail', function (e) {
        if (e.type === "click" || (e.type === "keypress" && e.keyCode === 13)) {
            let row = $(this).closest('tr');
            let data = table.row(row).data();
            Swal.fire({
                // title: 'LOG詳細資料',
                html: `
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            LOG詳細資料
                        </h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                        <dl class="sm:divide-y sm:divide-gray-200">
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    頻道
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['channel']+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    使用者
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['user']+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    IP
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['ip']+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    作業系統
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['platform']+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    瀏覽器
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['browser']+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    LOG等級
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['level']+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    訊息
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['message']+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    內文
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['context'].replace(/(\r\n|\n|\r|\\)/gm, "").replace(/&#34;/g, '"')+`
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    建立時間
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    `+data['created_at']+`
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                `,
                width: 600,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: `關閉`,
                background: "#f0efed",
            })
        }
    });
</script>
<?php include($root.'_layouts/manage/bottom.php') ?>

