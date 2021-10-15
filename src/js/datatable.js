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
});