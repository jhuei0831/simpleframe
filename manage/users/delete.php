<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use _models\Auth\User;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;
    use Kerwin\Core\Support\Facades\Session;

    $id = Security::defendFilter($_GET['id']);

    if ($id == Session::get('USER_ID')) {
        Message::flash('不能刪除自己', 'warning')->redirect(APP_ADDRESS.'manage/users');
    }

    $user = new User();
    $delete = $user->delete($id);
    $user->result($delete);