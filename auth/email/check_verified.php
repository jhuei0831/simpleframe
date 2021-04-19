<?php
    $root = '../../';
    include_once($root.'_config/settings.php');

    use _models\framework\Database as DB;
    use _models\framework\Message as MG;
    use _models\framework\Auth;

    if (isset($_GET['auth']) && isset($_GET['id'])) {
        if (!is_null(Auth::user()->updated_at)) {
            include_once($root.'_error/404.php');
            exit;
        }
        elseif (strtotime('now') > strtotime(Auth::user()->updated_at.' +30 minutes')) {
            MG::flash('驗證信已逾期，請重新獲取，謝謝。', 'warning');
            MG::redirect(APP_ADDRESS.'auth/email/verified.php');
        }
        elseif (Auth::id() != $_GET['id'] || Auth::user()->auth_code != $_GET['auth']) {
            MG::flash('連結有問題，請確認或重新申請認證信，謝謝。', 'warning');
            MG::redirect(APP_ADDRESS.'auth/email/verified.php');
        }
        else{
            DB::table('users')->where("id = '{$_SESSION['USER_ID']}'")->update(['token' => TOKEN, 'email_varified_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            MG::flash('信箱驗證成功，謝謝。', 'success');
            MG::redirect(APP_ADDRESS);
        }
    }
    
?>