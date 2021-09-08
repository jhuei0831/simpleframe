<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use _models\Auth\User;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;

    $id = Security::defendFilter($_GET['id']);

    if ($id == $_SESSION['USER_ID']) {
        Message::flash('不能刪除自己', 'warning');
        Message::redirect(APP_ADDRESS.'manage/users');
    }

    $user = new User();
    $user->delete($id);