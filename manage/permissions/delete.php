<?php
    $root = "../../";
    include($root.'_config/settings.php');

    use _models\Auth\Permission as PermissionInstance;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Permission;
    use Kerwin\Core\Support\Facades\Security;

    if (!Permission::can('permissions-delete')) {
        Message::flash('權限不足!', 'error')->redirect(APP_ADDRESS.'manage/permissions');
    }
    
    $id = Security::defendFilter($_GET['id']);

    $permission = new PermissionInstance();
    $delete = $permission->delete($id);