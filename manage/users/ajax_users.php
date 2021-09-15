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
        4 => 'id'
    );

    $datatable = new Datatable('users', $columns, $_REQUEST);
    $totalRecords = $datatable->totalRecords();
    $users = $datatable->query();
    // $recordsFiltered = count($users);
    $recordsFiltered = $datatable->resultFilterLength();

    $roles = Database::table('roles')->select('id', 'name')->get();
    $rolesFormat = [];
    
    foreach ($roles as $role) {
        $rolesFormat[$role->id] = $role->name;
    }

    foreach ($users as $user) {
        $user->role = ['display' => $rolesFormat[$user->role], 'filter' => $rolesFormat[$user->role]];
    }

    $data = array(
        "draw"            => intval($_REQUEST['draw']),   
        "recordsTotal"    => intval($totalRecords),  
        "recordsFiltered" => intval($recordsFiltered[0]->count),
        "data"            => $users,
    );

    echo json_encode($data);
?>