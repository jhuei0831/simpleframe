<?php

    $root= "../../";
    
    include($root.'_config/settings.php');

    use _models\framework\database as DB;
    use _models\framework\Message as MG;
    use _models\framework\Security as SC;
    use _models\framework\Toolbox as TB;
    use _models\framework\Permission;
    
    if (!Permission::can('roles-edit')) {
        MG::flash('Permission Denied!', 'error');
        MG::redirect(APP_ADDRESS.'manage/roles');
    }

    $id = SC::defend_filter($_GET['id']);
    $role = DB::table('roles')->find(SC::defend_filter($_GET['id']));
    $role_has_permissions = array_column(DB::table('role_has_permissions')->where('role_id = '.$role->id)->get(), 'permission_id');
    $permissions = DB::table('permissions')->get();

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = SC::defend_filter($_POST);
        $gump = new GUMP();

        // 輸入驗證
        $gump->validation_rules([
            'name'    => 'required|max_len,30',
        ]);

        // 輸入格式化
        $gump->filter_rules([
            'name'    => 'trim|sanitize_string',
        ]);

        $valid_data = $gump->run($data);

        $check_role = DB::table('roles')->where('name ="'.$valid_data['name'].'"')->count();

        if ($check_role > 0 && $role->name != $valid_data['name']) {
            $error = true;
            MG::flash('名稱已存在。', 'error');
        }
        elseif ($gump->errors()) {
            $error = true;
            MG::flash('修改失敗，請檢查輸入。', 'error');
            // MG::redirect(APP_ADDRESS.'manage/roles/edit.php?id='.$id);
        }
        else {
            DB::table('roles')->where('id = '.$id)->update(TB::only($valid_data, ['token', 'name']));
            DB::table('role_has_permissions')->where('role_id ='.$role->id)->delete();
            foreach ($valid_data['permission'] as $key => $value) {
                DB::table('role_has_permissions')->CreateOrUpdate(['token' => $valid_data['token'], 'permission_id' => $value, 'role_id' => $role->id]);
            }  
            MG::flash('修改成功，謝謝。', 'success');
            MG::redirect(APP_ADDRESS.'manage/roles');
        }
    }
    include($root.'_layouts/manage/top.php');
?>

<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Role Edit</h2>

    <!-- General elements -->
    <form method="post" id="form_role">
        <input type="hidden" name="token" value="<?=TOKEN?>">
        <?php if (isset($error) && $error): ?>
            <?php MG::show_flash();?>
            <div class="mb-4">
                <?php foreach($gump->get_readable_errors() as $error_message): ?>
                    <li><font color="red"><?=$error_message?></font></li>
                <?php endforeach; ?>
            </div>    
        <?php endif; ?>
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">Name</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="name" value="<?=isset($_POST['name'])?$_POST['name']:$role->name?>"
                        class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="Jane Doe" required
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Permissions</span>
                <div class="flex flex-wrap">
                
                <?php foreach($permissions as $permission): ?>
                    <label class="mt-4 mr-2 items-center dark:text-gray-400">
                        <input
                            type="checkbox" name="permission[]" value="<?=$permission->id?>" <?= in_array($permission->id, $role_has_permissions) ? 'checked' : '';?>
                            class="text-purple-600 form-checkbox focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                        />
                        <span class="ml-2"><?=$permission->name?></span>
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

<script>
    $("#form_role").validate({
        rules: {
            name: {
                required: true
            }   
        },
    });
</script>