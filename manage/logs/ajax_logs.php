<?php
    $root = '../../';
    include($root.'_config/settings.php');

    use _models\Datatable;

    $columns = array( 
        0 => 'user',
        1 => 'ip',
        2 => 'level',
        3 => 'message',
        4 => 'created_at',
        5 => 'id',
    );

    $datatable = new Datatable('logs', $columns, $_REQUEST);

    $data = $datatable->render();
    
    echo json_encode($data);
?>