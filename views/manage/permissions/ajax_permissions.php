<?php
    $root = '../../';
    include($root.'config/settings.php');

    use App\Models\Datatable;

    $columns = array( 
        0 => 'name',
        1 => 'description',
        2 => 'created_at',
        3 => 'id',
    );

    $datatable = new Datatable('permissions', $columns, $_REQUEST);

    $data = $datatable->render();
    
    echo json_encode($data);
?>