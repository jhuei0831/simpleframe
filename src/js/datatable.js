$(document).ready(function () {
    let table = $('#table').DataTable({
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
            title: 'Do you want to delete '+data['name']+' ?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
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