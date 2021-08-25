<?php
    $root= "../../";
    
    include($root.'_config/settings.php');

    use Kerwin\Core\Database;
    use Kerwin\Core\Message;
    use Kerwin\Core\Security;
    use Kerwin\Core\Toolbox;
    use Kerwin\Core\Permission;
    
    if (!Permission::can('roles-create')) {
        Message::flash('Permission Denied!', 'error');
        Message::redirect(APP_ADDRESS.'manage/roles');
    }

    $permissions = Database::table('permissions')->get();

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = Security::defend_filter($_POST);
        $gump = new GUMP();

        // 輸入驗證
        $gump->validation_rules([
            'name'    => 'required|max_len,30',
            'permission' => 'required'
        ]);

        // 輸入格式化
        $gump->filter_rules([
            'name'    => 'trim|sanitize_string',
        ]);

        $valid_data = $gump->run($data);

        $check_role = Database::table('roles')->where("name = '".$valid_data['name']."'")->count();

        if ($check_role > 0) {
            $error = true;
            Message::flash('名稱已存在。', 'error');
        }
        elseif ($gump->errors()) {
            $error = true;
            Message::flash('新增失敗，請檢查輸入。', 'error');
            // Message::redirect(APP_ADDRESS.'manage/roles/edit.php?id='.$id);
        }
        else {
            $insert = Database::table('roles')->insert(Toolbox::only($valid_data, ['token', 'name']), TRUE);
            foreach ($valid_data['permission'] as $key => $value) {
                Database::table('role_has_permissions')->CreateOrUpdate(['token' => $valid_data['token'], 'permission_id' => $value, 'role_id' => $insert]);
            }  
            Message::flash('新增成功。', 'success');
            Message::redirect(APP_ADDRESS.'manage/roles');
        }
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
            <?php Message::show_flash();?>
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
