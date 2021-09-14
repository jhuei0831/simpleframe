<?php
    $root = '../../';
    include($root.'_config/settings.php');

    use _models\Datatable;
    use Kerwin\Core\Support\Facades\Database;

    $columns = array( 
        0 => 'name',
        1 => 'email',
        2 => 'role',
        3 => 'created_at',
    );

    $datatable = new Datatable('users', $columns, $_REQUEST);
    $totalRecords = $datatable->totalRecords();
    $users = $datatable->query();
    $recordsFiltered = count($users);

    $roles = Database::table('roles')->select('id', 'name')->get();
    $rolesFormat = [];
    
    foreach ($roles as $role) {
        $rolesFormat[$role->id] = $role->name;
    }

    foreach ($users as $user) {
        $user->role = $rolesFormat[$user->role];
    }

    $data = array(
        "draw"            => intval($_REQUEST['draw']),   
        "recordsTotal"    => intval($totalRecords),  
        "recordsFiltered" => intval($recordsFiltered),
        "data"            => $users,
    );

    echo json_encode($data);
?>