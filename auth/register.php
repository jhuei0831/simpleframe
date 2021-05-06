<?php
    $root = '../';
    $page_title = 'Register';
    include_once($root.'_config/settings.php');

    use _models\framework\Security as SC;
    use _models\framework\Database as DB;
    use _models\framework\Message as MG;
    use _models\framework\Toolbox;
    use _models\framework\Mail;

    if (!is_null($_SESSION['USER_ID'])) {
        MG::redirect(APP_ADDRESS);
    }
    
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = SC::defend_filter($_POST);
        $gump = new GUMP();

        // 輸入驗證
        $gump->validation_rules([
            'name'    => 'required|alpha_numeric|max_len,30',
            'email'   => 'required|valid_email',
            'password'    => 'required|max_len,30|min_len,8',
            'password_confirm'    => 'required|max_len,30|min_len,8',
        ]);

        // 輸入格式化
        $gump->filter_rules([
            'name'    => 'trim|sanitize_string',
            'email'   => 'trim|sanitize_email',
            'password'    => 'trim',
            'password_confirm'   => 'trim',
        ]);

        $valid_data = $gump->run($data);

        $check_user = DB::table('users')->where('email ="'.$valid_data['email'].'"')->first();
        $errors = [];
        if ($check_user) {
            MG::flash('信箱已被註冊使用', 'error');
        }
        elseif ($valid_data['password'] != $valid_data['password_confirm']) {
            MG::flash('密碼要和確認密碼相同', 'error');
        }
        elseif ($gump->errors()) {
            $errors[] = $gump->get_readable_errors();
            MG::flash('註冊失敗，請檢查輸入', 'error');
        }
        else {
            unset($valid_data['password_confirm']);
            $auth_code = uniqid(mt_rand());
            $valid_data['password'] = md5($valid_data['password']);
            $valid_data['id'] = Toolbox::UUIDv4();
            $valid_data['role'] = 2;
            $valid_data['auth_code'] = $auth_code;
            $insert = DB::table('users')->insert($valid_data, TRUE);
            // 取得剛剛註冊的帳號ID
            $insert_id = DB::table('users')->where("email = '{$valid_data['email']}'")->first();
            $id = $insert_id->id;
            $name = $valid_data['name'];
            include_once('./email/content.php');
            $mail = Mail::send($subject, $message, $valid_data['email'], $valid_data['name']);
            $_SESSION['USER_ID'] = $id;
            if ($mail) {
                MG::flash('註冊成功，請前往註冊信箱收取認證信。', 'success');
                MG::redirect(APP_ADDRESS.'auth/email/verified.php');
            }   
        }
    }
    MG::show_flash();
    include_once($root.'_layouts/auth/top.php');
?>
<div class="flex items-center justify-center bg-gray-50 py-32 px-4 sm:px-6 lg:px-8" x-data={loading:false}>
    <div class="max-w-md w-full space-y-8 mt-12">
        <div>
            <a href="<?=APP_ADDRESS?>">
                <img :class="{ 'animate-spin': loading === true }" class="mx-auto h-12 w-auto" src="<?=APP_IMG?>grapes.png" alt="Workflow">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign up your account
            </h2>
        </div>
        <form id="form_register" class="mt-8 space-y-6" method="POST" @submit="loading = true">
            <input type="hidden" name="token" value="<?=TOKEN?>">
            <div class="mb-4">
            <?php include_once($root.'_partials/error_message.php'); ?>
            </div> 
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="name" class="sr-only">Name</label>
                    <input id="name" name="name" type="text" value="<?=isset($data['name'])?$data['name']:''?>" autocomplete="name" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Name">
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" value="<?=isset($data['email'])?$data['email']:''?>" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
                <div>
                    <label for="password_confirm" class="sr-only">Password Confirm</label>
                    <input id="password_confirm" name="password_confirm" type="password" autocomplete="confirm-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password Confirm">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                        </svg>
                    </span>
                    Sign up
                </button>
            </div>
        </form>
    </div>
</div>
<?php include_once($root.'_layouts/auth/bottom.php'); ?>


