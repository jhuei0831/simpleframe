<?php
    $root = '../../';

    use _models\database as DB;
    use _models\Message as MG;
    use _models\Security as SC;

    include($root.'_config/settings.php');
    include($root.'_layouts/manage/top.php');

    $id = SC::defend_filter($_GET['id']);
    $user = DB::table('users')->find($id);
    $roles = DB::table('roles')->get();

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = SC::defend_filter($_POST);
        if ($data['type'] == 'profile') {
            $profile_error = false;
            unset($data['type']);
            $gump = new GUMP();

            // 輸入驗證
            $gump->validation_rules([
                'name'    => 'required|alpha_numeric|max_len,30',
                'email'   => 'required|valid_email',
                'role'    => 'required'
            ]);

            // 輸入格式化
            $gump->filter_rules([
                'name'    => 'trim|sanitize_string',
                'email'   => 'trim|sanitize_email',
            ]);

            $valid_data = $gump->run($data);
            
            if (!$gump->errors()) {
                $update = DB::table('users')->where('id = '.$id)->update($valid_data);
                MG::flash('修改成功，謝謝。', 'success');
                MG::redirect(APP_ADDRESS.'manage/users');
            }
            else {
                $profile_error = true;
                MG::flash('修改失敗，請檢查輸入。', 'error');
                // MG::redirect(APP_ADDRESS.'manage/users/edit.php?id='.$id);
            }
        }
        else {
            $password_error = false;
            unset($data['type']);
            $gump = new GUMP();

            // 輸入驗證
            $gump->validation_rules([
                'password'    => 'required|max_len,30|min_len,8',
                'password_confirm'    => 'required|max_len,30|min_len,8',
            ]);

            // 輸入格式化
            $gump->filter_rules([
                'password'    => 'trim',
                'password_confirm'   => 'trim',
            ]);
            $valid_data = $gump->run($data);

            if ($data['password'] != $data['password_confirm']) {
                $password_error = true;
                MG::flash('密碼要和確認密碼相同!。', 'error');
            }
            elseif ($gump->errors()) {
                $password_error = true;
                MG::flash('修改失敗，請檢查輸入。', 'error');
                // MG::redirect(APP_ADDRESS.'manage/users/edit.php?id='.$id); 
            }
            else {
                unset($valid_data['password_confirm']);
                $valid_data['password'] = md5($valid_data['password']);
                $update = DB::table('users')->where('id = '.$id)->update($valid_data);
                MG::flash('修改成功，謝謝。', 'success');
                MG::redirect(APP_ADDRESS.'manage/users');
            }
        }
    }
?>    
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">User Edit</h2>

    <!-- General elements -->
    <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">Personal Profile</h4>
    <form method="post" id="form_profile">
        <input type="hidden" name="type" value="profile">
        <input type="hidden" name="token" value="<?=TOKEN?>">
        <?php if (isset($profile_error) && $profile_error): ?>
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
                        name="name" value="<?=isset($_POST['name'])?$_POST['name']:$user['name']?>"
                        class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="Jane Doe" required
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Email</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="email" value="<?=isset($_POST['email'])?$_POST['email']:$user['email']?>"
                        class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="example@example.com" required
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Role</span>
                <select
                    name="role"
                    class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                >
                    <?php foreach($roles as $role): ?>
                        <option value="<?=$role['id']?>" <?= $user['role'] == $role['id'] ? 'selected' : '' ?> ><?=$role['name']?></option>
                    <?php endforeach;?>
                </select>
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
    
    <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">Password</h4>
    <form id="form_password" method="post">
        <input type="hidden" name="type" value="password">
        <input type="hidden" name="token" value="<?=TOKEN?>">
        <?php if (isset($password_error) && $password_error): ?>
            <?php MG::show_flash();?>
            <div class="mb-4">
                <?php foreach($gump->get_readable_errors() as $error_message): ?>
                    <li><font color="red"><?=$error_message?></font></li>
                <?php endforeach; ?>
            </div>    
        <?php endif; ?>
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">Password</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="password" type="password"
                        class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="*********"
                    />
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Password Confirm</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="password_confirm" type="password"
                        class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="*********"
                    />
                </div>
            </label>
            <div class="flex justify-end">
                <button
                    class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                >
                    送出
                </button>
            </div>
        </form>
    </div>
</div>

<?php include($root.'_layouts/manage/bottom.php'); ?>

<script>
    $("#form_profile").validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            role: {
                required: true
            }        
        },
    });
    $("#form_password").validate({
        rules: {
            password: {
                required: true,
                rangelength: [8, 30]
            },
            password_confirm: {
                required: true,
                min: 8,
                rangelength: [8, 30]
            }      
        },
    });
</script>