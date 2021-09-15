<?php
    $root = '../../';
    include($root.'_config/settings.php');

    use _models\Datatable;
    use Kerwin\Core\Support\Facades\Database;

    $columns = array( 
        0 => 'users.name',
        1 => 'users.email',
        2 => 'roles.name',
        3 => 'users.created_at',
        4 => 'users.id'
    );

    $datatable = new Datatable('users', $columns, $_REQUEST);
    $totalRecords = $datatable->totalRecords();
    $where = $datatable->filter();
    /* 因為要把角色的代碼轉中文並搜尋，所以join角色資料表 */
    $rolesCondition = ($_REQUEST['columns'][2]['search']['value'] == '') ? '' : "and roles.name like '%".$_REQUEST['columns'][2]['search']['value']."%'";
    $users = Database::table('users')
        ->select('users.name as "users.name", users.email as "users.email", roles.name, users.created_at, users.id')
        ->where($where)
        ->join('roles', 'roles.id = users.role '.$rolesCondition)
        ->orderby([[$columns[$_REQUEST['order'][0]['column']], $_REQUEST['order'][0]['dir']]])
        ->limit($_REQUEST['start'], $_REQUEST['length'])
        ->get();

    $recordsFiltered = $datatable->resultFilterLength();

    $data = array(
        "draw"            => intval($_REQUEST['draw']),   
        "recordsTotal"    => intval($totalRecords),  
        "recordsFiltered" => intval($recordsFiltered[0]->count),
        "data"            => $users,
    );

    echo json_encode($data);
?>