<?php
    $root= "../../";
    
    include($root.'_config/settings.php');

    use _models\Auth\Permission as PermissionInstance;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Permission;
    use Kerwin\Core\Support\Facades\Security;
    
    if (!Permission::can('permissions-create')) {
        Message::flash('權限不足!', 'error')->redirect(APP_ADDRESS.'manage/permissions');
    }

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $permission = PermissionInstance::getInstance();
        $permission->create($_POST);
        $errors = $permission->errors;
    }

    Message::showFlash();
    include($root.'_layouts/manage/top.php');
?>
<!-- breadcrumb -->
<?php echo Toolbox::breadcrumb(APP_ADDRESS.'manage', ['權限管理'=> APP_ADDRESS.'manage/permissions', '新增權限' => '#'])?>

<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">新增權限</h2>
    <form id="form_permission" method="post">
        <input type="hidden" name="token" value="<?php echo TOKEN?>">
        <div class="mb-4">
            <?php include_once($root.'_partials/error_message.php'); ?>
        </div>
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">名稱</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="name" value="<?php echo isset($_POST['name']) ? Security::defendFilter($_POST['name']) : '' ?>" type="text"
                        class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300"
                        required/>
                    <div class="mt-2 absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">敘述</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="description" value="<?php echo isset($_POST['description']) ? Security::defendFilter($_POST['description']) : '' ?>" type="text"
                        class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300"
                        required/>
                    <div class="mt-2 absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-card-text"></i>
                    </div>
                </div>
            </label>
            <div class="flex justify-end">
                <button
                    class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    送出
                </button>
            </div>
        </div>
    </form>
</div>

<?php include($root.'_layouts/manage/bottom.php'); ?>
