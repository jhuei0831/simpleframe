import Swal from 'sweetalert2';
$(document).ready(function () {
    
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
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
            url: url, 
            type: "post", 
            error: function(res){
                console.log(res)
            }
        },
        "columns": columns
    });

    // row edit
    $('#table tbody').on('click', '.edit', function () {
        let row = $(this).closest('tr');
        let data = table.row(row).data()['id'];
        window.location.href = './edit.php?id='+data;
    });

    // row delete
    $('#table tbody').on('click', '.delete', function () {
        let row = $(this).closest('tr');
        let data = table.row(row).data();
        Swal.fire({
            title: '確定要刪除 '+data['name']+' ?',
            showCancelButton: true,
            confirmButtonText: `是`,
            cancelButtonText: `否`,
            confirmButtonColor: 'LightSeaGreen',
            cancelButtonColor: '#ffbdc5',
            background: "#fffbf2",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = './delete.php?id='+data['id'];
            }
        })
    });
});