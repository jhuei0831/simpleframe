<?php
    $root = "../../";
    include($root.'config/settings.php');

    use App\Models\Auth\Role;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;
    use Kerwin\Core\Support\Facades\Permission;
    
    if (!Permission::can('roles-delete')) {
        Message::flash('權限不足!', 'error')->redirect(APP_ADDRESS.'manage/roles');
    }

    $id = Security::defendFilter($_GET['id']);
    $role = new Role();
    $role->delete($id);