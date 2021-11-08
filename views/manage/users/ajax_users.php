<?php
    $root = '../../';
    include($root.'config/settings.php');

    use models\Datatable;

    // $columns = array( 
    //     'select' => [
    //         0 => ['column' => 'users.name', 'as' => 'name'],
    //         1 => ['column' => 'users.email', 'as' => 'email'],
    //         2 => ['column' => 'roles.name', 'as' => 'role'],
    //         3 => ['column' => 'users.created_at', 'as' => 'created_at'],
    //         4 => ['column' => 'users.id', 'as' => 'id']
    //     ],
    //     'join' => [
    //         ['column' => 2, 'table' => 'roles', 'on' => 'roles.id = users.role', 'condition' => 'roles.name']
    //     ]
    // );

    $columns = array( 
        0 => 'name',
        1 => 'email',
        2 => 'role',
        3 => 'created_at',
        4 => 'id',
    );

    $datatable = new Datatable('users', $columns, $_REQUEST);

    $data = $datatable->render();

    echo json_encode($data);
?>