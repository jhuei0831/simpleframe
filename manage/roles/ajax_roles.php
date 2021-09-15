<?php
    $root = '../../';
    include($root.'_config/settings.php');

    use _models\Datatable;

    $columns = array( 
        0 => 'name',
        1 => 'created_at',
        2 => 'id',
    );

    $datatable = new Datatable('roles', $columns, $_REQUEST);

    $data = $datatable->render();
    
    echo json_encode($data);
?>