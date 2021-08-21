<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use Kerwin\Core\Database;
    use Kerwin\Core\Message;
    use Kerwin\Core\Security;

    $id = Security::defend_filter($_GET['id']);

    if ($id == $_SESSION['USER_ID']) {
        Message::flash('不能刪除自己', 'warning');
        Message::redirect(APP_ADDRESS.'manage/users');
    }
    Database::table('users')->where('id='.Security::defend_filter($_GET['id']))->delete();
    Message::flash('刪除成功，謝謝。', 'success');
    Message::redirect(APP_ADDRESS.'manage/users');