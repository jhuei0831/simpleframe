<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use Kerwin\Core\Database;
    use Kerwin\Core\Message;
    use Kerwin\Core\Security;
    use Kerwin\Core\Permission;
    
    if (!Permission::can('roles-delete')) {
        Message::flash('Permission Denied!', 'error');
        Message::redirect(APP_ADDRESS.'manage/roles');
    }
    $id = Security::defend_filter($_GET['id']);
    $check = Database::table('users')->where('role ='.$id)->count();
    if ($check > 0) {
        Message::flash('此角色尚有使用者使用', 'warning');
        Message::redirect(APP_ADDRESS.'manage/roles');
    }
    Database::table('roles')->where('id='.$id)->delete();
    Message::flash('刪除成功，謝謝。', 'success');
    Message::redirect(APP_ADDRESS.'manage/roles');