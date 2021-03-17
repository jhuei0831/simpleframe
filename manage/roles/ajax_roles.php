<?php
    $root = '../../';
    include($root.'_config/settings.php');

    use _models\Database as DB;

    $where="1=1";
    if( !empty($_REQUEST['search']['value']) ) { 
        $where.=" and id LIKE '%".$_REQUEST['search']['value']."%' ";    
        $where.="OR name LIKE '%".$_REQUEST['search']['value']."%'";
    }
    $count = DB::table('roles')->get();
    $totalRecords=count($count);
    
    $columns = array( 
        0 => 'id', 
        1 => 'name',
        2 => 'created_at',
    );

    $roles = DB::table('roles')
        ->select('id', 'name', 'created_at')
        ->where($where)
        ->orderby([[$columns[$_REQUEST['order'][0]['column']], $_REQUEST['order'][0]['dir']]])
        ->limit($_REQUEST['start'], $_REQUEST['length'])
        ->get();

    $json_data = array(
        "draw"            => intval($_REQUEST['draw']),   
        "recordsTotal"    => intval($totalRecords),  
        "recordsFiltered" => intval($totalRecords),
        "data"            => $roles,
    );

    echo json_encode($json_data);
?>