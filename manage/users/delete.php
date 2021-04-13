<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use _models\database as DB;
    use _models\message as MG;
    use _models\Security as SC;

    $id = SC::defend_filter($_GET['id']);

    if ($id == $_SESSION['USER_ID']) {
        MG::flash('不能刪除自己', 'warning');
        MG::redirect(APP_ADDRESS.'manage/users');
    }
    DB::table('users')->where('id='.SC::defend_filter($_GET['id']))->delete();
    MG::flash('刪除成功，謝謝。', 'success');
    MG::redirect(APP_ADDRESS.'manage/users');