import Swal from 'sweetalert2';

$(document).ready(function () {

    //按下表單的查詢按鈕後才發出Ajax載入資料 
    $("#btnQuery").on('click', function () {
        table.draw(); //或table.ajax.reload();

    });

    // 編輯按鈕導向
    $('#table tbody').on('click', '.edit', function () {
        let row = $(this).closest('tr');
        let data = table.row(row).data()['id'];
        window.location.href = './edit.php?id=' + data;
    });

    // 刪除按鈕導向
    $('#table tbody').on('click', '.delete', function () {
        let row = $(this).closest('tr');
        let data = table.row(row).data();
        Swal.fire({
            title: '確定要刪除 ' + data['name'] + ' ?',
            showCancelButton: true,
            confirmButtonText: `是`,
            cancelButtonText: `否`,
            confirmButtonColor: 'LightSeaGreen',
            cancelButtonColor: '#ffbdc5',
            background: "#fffbf2",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = './delete.php?id=' + data['id'];
            }
        })
    });

    // 詳細資料按鈕導向
    $('#table tbody').on('click', '.detail', function () {
        let row = $(this).closest('tr');
        let data = table.row(row).data();
        console.log(JSON.parse(data['context'].replace(/(\r\n|\n|\r|\\)/gm, "").replace(/&#34;/g, '"')));
        Swal.fire({
            // title: 'LOG詳細資料',
            html: `
            <div class="bg-white shadow overflow-hidden sm:rounded-lg w-full">
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
            // background: "#000",
        })
    });
});