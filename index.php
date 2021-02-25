<?php
    include('_config/settings.php');
    use _models\database as DB;
    use _models\test;
    $db = new DB();
    // $users = $db->select('name', 'email')->table('users')->where('id = 1')->result()->get();
    $user = $db->table('users')->find(2);
    $db = new DB();
    $user2 = $db->table('users')->get();
    // print_r($user);
    // print_r($user2);

    echo test::init(5)->add(2)->out();
    echo test::init(5)->add(2)->out();
?>