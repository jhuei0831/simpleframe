<?php
    $root = '../../';
    include($root.'_config/settings.php');

    use Kerwin\Core\Database;

    $where="1=1";
    if( !empty($_REQUEST['search']['value']) ) { 
        $where.=" and id LIKE '%".$_REQUEST['search']['value']."%' ";    
        $where.="OR name LIKE '%".$_REQUEST['search']['value']."%'";
        $where.="OR email LIKE '%".$_REQUEST['search']['value']."%'";
    }
    $count = Database::table('users')->get();
    $totalRecords=count($count);
    
    $columns = array( 
        0 => 'id', 
        1 => 'name',
        2 => 'email',
        3 => 'created_at',
    );

    $roles = Database::table('users')
        ->select('id', 'name', 'email', 'created_at')
        ->where($where)
        ->orderby([[$columns[$_REQUEST['order'][0]['column']+1], $_REQUEST['order'][0]['dir']]])
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