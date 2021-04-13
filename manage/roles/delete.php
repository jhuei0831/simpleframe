<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use _models\database as DB;
    use _models\message as MG;
    use _models\Security as SC;
    use _models\Permission;
    
    if (!Permission::can('roles-delete')) {
        MG::flash('Permission Denied!', 'error');
        MG::redirect(APP_ADDRESS.'manage/roles');
    }
    $id = SC::defend_filter($_GET['id']);
    $check = DB::table('users')->where('role ='.$id)->count();
    if ($check > 0) {
        MG::flash('此角色尚有使用者使用', 'warning');
        MG::redirect(APP_ADDRESS.'manage/roles');
    }
    DB::table('roles')->where('id='.$id)->delete();
    MG::flash('刪除成功，謝謝。', 'success');
    MG::redirect(APP_ADDRESS.'manage/roles');