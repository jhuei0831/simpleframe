<?php
    $root = '../../';
    $page_title = 'Password Reset';
    include_once($root.'_config/settings.php');

    use _models\framework\Security as SC;
    use _models\framework\Database as DB;
    use _models\framework\Message as MG;
    use _models\framework\Auth;

    Auth::password_reset();

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = SC::defend_filter($_POST);
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

        $errors = [];
        
        $password_resets = DB::table('password_resets')->where("id='{$_GET['id']}' and email_token='{$_GET['auth']}'")->first(false);
        $password = json_decode($password_resets->password);
        if ($gump->errors()) {
            $errors[] = $gump->get_readable_errors();
            MG::flash('重設密碼失敗，請檢查輸入', 'error');
        }
        elseif ($valid_data['password'] != $valid_data['password_confirm']) {
            MG::flash('密碼要和確認密碼相同', 'error');
        }
        elseif (in_array(md5($valid_data['password']), $password)) {
            MG::flash('密碼不能與前三次相同', 'error');
        }
        else {
            unset($valid_data['password_confirm']);
            $valid_data['password'] = md5($valid_data['password']);
            // 如果密碼有三筆，清除第一筆
			if (count($password) == 3) {
				$shift = array_shift($password);
				array_push($password, $valid_data['password']);
			}
			else {
				array_push($password, $valid_data['password']);
			}

            // 更新使用者密碼
            $update_users = DB::table('users')
                ->where("id='{$password_resets->id}'")
                ->update([
                    'token' => $valid_data['token'],
                    'password' => $valid_data['password'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // 更新密碼重設資料
            $update_password_resets = DB::table('password_resets')
                ->where("id='{$password_resets->id}'")
                ->update([
                    'token' => $valid_data['token'], 
                    'password' => json_encode($password),
                    'password_updated_at' => date('Y-m-d H:i:s'),
                ]);
            if ($update_users && $update_password_resets) {
                MG::flash('密碼修改成功，請使用新密碼登入。', 'success');
                MG::redirect(APP_ADDRESS.'auth/login.php');
            }   
        }
    }
    MG::show_flash();
    include_once($root.'_layouts/auth/top.php');
?>
<div class="flex h-full items-center justify-center bg-gray-50 pb-32 px-4 sm:px-6 lg:px-8" x-data={loading:false}>
    <div class="max-w-md w-full space-y-8 mt-12">
        <div>
            <a href="<?=APP_ADDRESS?>">
                <img :class="{'animate-spin': loading === true}" class="mx-auto h-12 w-auto" src="<?=APP_IMG?>grapes.png" alt="Workflow">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Password Reset
            </h2>
        </div>
        <form class="mt-8 space-y-6" method="POST" id="form_reset" @submit="loading = true">
            <input type="hidden" name="token" value="<?=TOKEN?>">
            <div class="rounded-md shadow-sm -space-y-px text-center">
                請輸入新的密碼，謝謝。
            </div>

            <div class="mb-4">
                <?php include_once($root.'_partials/error_message.php'); ?>
            </div> 
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-t-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
                <div>
                    <label for="password_confirm" class="sr-only">Password Confirm</label>
                    <input id="password_confirm" name="password_confirm" type="password" autocomplete="confirm-password" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password Confirm">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    送出
                </button>
            </div>
        </form>
    </div>
</div>
<?php include_once($root.'_layouts/auth/bottom.php'); ?>
<script>
    $("#form_reset").validate({
        rules: {
            password: {
                required: true,
                rangelength: [8, 30]
            },
            password_confirm: {
                required: true,
                rangelength: [8, 30]
            }       
        },
    });
</script>

