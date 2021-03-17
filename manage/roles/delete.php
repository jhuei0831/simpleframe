<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use _models\database as DB;
    use _models\message as MG;
    use _models\Security as SC;

    DB::table('roles')->where('id='.SC::defend_filter($_GET['id']))->delete();
    MG::flash('刪除成功，謝謝。', 'success');
    MG::redirect(APP_ADDRESS.'manage/roles');