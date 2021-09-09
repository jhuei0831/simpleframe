<?php
    $root= "../../";
    
    include($root.'_config/settings.php');

    use _models\Auth\Role;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Permission;
    
    if (!Permission::can('roles-create')) {
        Message::flash('權限不足!', 'error')->redirect(APP_ADDRESS.'manage/roles');
    }

    $permissions = Database::table('permissions')->get();

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $role = new Role();
        $role->create($_POST);
    }

    include($root.'_layouts/manage/top.php');
?>
<!-- breadcrumb -->
<?php echo Toolbox::breadcrumb(APP_ADDRESS.'manage', ['角色管理'=> APP_ADDRESS.'manage/roles', '新增角色' => '#'])?>

<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">新增角色</h2>
    <form method="post" id="form_role">
        <input type="hidden" name="token" value="<?php echo TOKEN?>">
        <?php if (isset($error) && $error): ?>
            <?php Message::showFlash();?>
            <div class="mb-4">
                <?php foreach($gump->get_readable_errors() as $error_message): ?>
                    <li><font color="red"><?php echo $error_message?></font></li>
                <?php endforeach; ?>
            </div>    
        <?php endif; ?>
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">名稱</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="name" type="text"
                        class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300"
                        placeholder="Jane Doe" required
                    />
                    <div class="mt-2 absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">權限</span>
                <div class="flex flex-wrap">
                
                <?php foreach($permissions as $permission): ?>
                    <label class="mt-4 mr-2 items-center dark:text-gray-400">
                        <input
                            type="checkbox" name="permission[]" value="<?php echo $permission->id?>"
                            class="text-purple-600 form-checkbox focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                        />
                        <span class="ml-2"><?php echo $permission->name?></span>
                    </label>
                <?php endforeach; ?>
                </div>
            </label>
            <div class="flex justify-end">
                <button
                    class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                >
                    送出
                </button>
            </div>
        </div>     
    </form>
</div>

<?php include($root.'_layouts/manage/bottom.php'); ?>
