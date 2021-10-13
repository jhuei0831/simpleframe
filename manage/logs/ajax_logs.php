<?php
    $root = '../../';
    include($root.'_config/settings.php');

    use _models\Datatable;

    $columns = array( 
        0 => 'id',
        1 => 'ip',
        2 => 'level',
        3 => 'message',
        4 => 'created_at',
        5 => 'browser',
        6 => 'user',
        7 => 'channel',
        8 => 'context',
        9 => 'platform',
    );

    $datatable = new Datatable('logs', $columns, $_REQUEST);

    $data = $datatable->render();
    
    echo json_encode($data);
?>