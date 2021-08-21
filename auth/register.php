<?php
    $root = '../';
    $page_title = 'Register';
    include_once($root.'_config/settings.php');

    use Kerwin\Core\Security;
    use Kerwin\Core\Database;
    use Kerwin\Core\Message;
    use Kerwin\Core\Toolbox;
    use Kerwin\Core\Mail;

    if (!is_null($_SESSION['USER_ID'])) {
        Message::redirect(APP_ADDRESS);
    }
    
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = Security::defend_filter($_POST);
        $gump = new GUMP();

        // 輸入驗證
        $gump->validation_rules([
            'name'    => 'required|max_len,30',
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

        $check_user = Database::table('users')->where('email ="'.$valid_data['email'].'"')->first();
        $errors = [];
        // 密碼規則驗證
        $SafeCheck = array();
        if (PASSWORD_SECURE === 'TRUE') {

            if(preg_match('/(?=.*[a-z])/',$_POST['password']))
            {
                array_push($SafeCheck, '有英文小寫');
            }
            if(preg_match('/(?=.*[A-Z])/',$_POST['password']))
            {
                array_push($SafeCheck, '有英文大寫');
            }
            if(preg_match('/(?=.*[0-9])/',$_POST['password']))
            {
                array_push($SafeCheck, '有數字');
            }
            if(preg_match('/[\Q!@#$%^&*+-\E]/',$_POST['password']))
            {
                array_push($SafeCheck, '有特殊符號');
            }
        }
        if ($check_user) {
            Message::flash('信箱已被註冊使用', 'error');
        }
        elseif (PASSWORD_SECURE === 'TRUE' && (count($SafeCheck) <= 3 || !preg_match('/.{8,}/',$valid_data['password']))) {
            Message::flash('密碼不符合規則，請參考密碼規則並再次確認', 'error');
        }
        elseif ($valid_data['password'] != $valid_data['password_confirm']) {
            Message::flash('密碼要和確認密碼相同', 'error');
        }
        elseif ($gump->errors()) {
            $errors[] = $gump->get_readable_errors();
            Message::flash('註冊失敗，請檢查輸入', 'error');
        }
        else {
            unset($valid_data['password_confirm']);
            $auth_code = uniqid(mt_rand());
            $valid_data['password'] = md5($valid_data['password']);
            $valid_data['id'] = Toolbox::UUIDv4();
            $valid_data['role'] = 2;
            $valid_data['auth_code'] = $auth_code;
            $insert = Database::table('users')->insert($valid_data, TRUE);
            // 取得剛剛註冊的帳號ID
            $insert_id = Database::table('users')->where("email = '{$valid_data['email']}'")->first();
            $id = $insert_id->id;
            $_SESSION['USER_ID'] = $id;
            if (EMAIL_VERIFY==='TRUE') {
                $name = $valid_data['name'];
                include_once('./email/content.php');
                $mail = Mail::send($subject, $message, $valid_data['email'], $valid_data['name']);
                Message::flash('註冊成功，請前往註冊信箱收取認證信。', 'success');
                Message::redirect(APP_ADDRESS.'auth/email/verified.php');
            }
            else {
                Message::flash('註冊成功。', 'success');
                Message::redirect(APP_ADDRESS);
            }
        }
    }
    Message::show_flash();
    include_once($root.'_layouts/auth/top.php');
?>
<div class="flex items-center justify-center bg-gray-50 py-32 px-4 sm:px-6 lg:px-8" x-data="{loading: false, password: '', password_confirm: ''}">
    <div class="max-w-md w-full space-y-8 mt-12">
        <div>
            <a href="<?php echo APP_ADDRESS?>">
                <img :class="{ 'animate-spin': loading === true }" class="mx-auto h-12 w-auto" src="<?php echo APP_IMG?>grapes.png" alt="Workflow">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign up your account
            </h2>
        </div>
        <form id="form_register" class="mt-8 space-y-6" method="POST" @submit="loading = true">
            <input type="hidden" name="token" value="<?php echo TOKEN?>">
            <div class="mb-4">
                <?php include_once($root.'_partials/error_message.php'); ?>
            </div> 
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="name" class="sr-only">Name</label>
                    <input id="name" name="name" type="text" value="<?php echo isset($data['name'])?$data['name']:''?>" autocomplete="name" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Name">
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" value="<?php echo isset($data['email'])?$data['email']:''?>" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" x-model="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
                <div>
                    <label for="password_confirm" class="sr-only">Password Confirm</label>
                    <input id="password_confirm" name="password_confirm" x-model="password_confirm" type="password" autocomplete="confirm-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password Confirm">
                </div>
            </div>

            <div class="flex justify-start mt-3 ml-4 p-1">
                <ul>
                    <li class="flex items-center py-1">
                        <span>Password Rule</span>
                    </li>
                    <li class="flex items-center py-1">
                        <div :class="{'bg-green-200 text-green-700': password == password_confirm && password.length > 0, 'bg-red-200 text-red-700':password != password_confirm || password.length == 0}" class=" rounded-full p-1 fill-current ">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password == password_confirm && password.length > 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                <path x-show="password != password_confirm || password.length == 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span :class="{'text-green-700': password == password_confirm && password.length > 0, 'text-red-700':password != password_confirm || password.length == 0}" class="font-medium text-sm ml-3" x-text="password == password_confirm && password.length > 0 ? 'Passwords match' : 'Passwords do not match' "></span>
                    </li>
                    <li class="flex items-center py-1">
                        <div :class="{'bg-green-200 text-green-700': password.length > 7, 'bg-red-200 text-red-700':password.length <= 7 }" class=" rounded-full p-1 fill-current ">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password.length > 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                <path x-show="password.length <= 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span :class="{'text-green-700': password.length > 7, 'text-red-700':password.length <= 7 }" class="font-medium text-sm ml-3" x-text="password.length > 7 ? 'The minimum length is reached' : 'At least 8 characters required' "></span>
                    </li>
                    <?if (PASSWORD_SECURE === 'TRUE'):?>
                    <li class="flex items-center py-1">
                        <div :class="{'bg-green-200 text-green-700': password.search(/[0-9]/) >= 0, 'bg-red-200 text-red-700':password.search(/[0-9]/) < 0 }" class=" rounded-full p-1 fill-current ">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password.search(/[0-9]/) >= 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                <path x-show="password.search(/[0-9]/) < 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span :class="{'text-green-700': password.search(/[0-9]/) >= 0, 'text-red-700':password.search(/[0-9]/) < 0 }" class="font-medium text-sm ml-3" x-text="password.search(/[0-9]/) >= 0 ? 'An digit is reached' : 'At lease one digit required' "></span>
                    </li>
                    <li class="flex items-center py-1">
                        <div :class="{'bg-green-200 text-green-700': password.search(/[A-Z]/) >= 0, 'bg-red-200 text-red-700':password.search(/[A-Z]/) < 0 }" class=" rounded-full p-1 fill-current ">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password.search(/[A-Z]/) >= 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                <path x-show="password.search(/[A-Z]/) < 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span :class="{'text-green-700': password.search(/[A-Z]/) >= 0, 'text-red-700':password.search(/[A-Z]/) < 0 }" class="font-medium text-sm ml-3" x-text="password.search(/[A-Z]/) >= 0 ? 'An upper case letter is reached' : 'At lease one upper case letter required' "></span>
                    </li>
                    <li class="flex items-center py-1">
                        <div :class="{'bg-green-200 text-green-700': password.search(/[a-z]/) >= 0, 'bg-red-200 text-red-700':password.search(/[a-z]/) < 0 }" class=" rounded-full p-1 fill-current ">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password.search(/[a-z]/) >= 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                <path x-show="password.search(/[a-z]/) < 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span :class="{'text-green-700': password.search(/[a-z]/) >= 0, 'text-red-700':password.search(/[a-z]/) < 0 }" class="font-medium text-sm ml-3" x-text="password.search(/[a-z]/) >= 0 ? 'An lower case letter is reached' : 'At lease one lower case letter required' "></span>
                    </li>
                    <li class="flex items-center py-1">
                        <div :class="{'bg-green-200 text-green-700': password.search(/[!@#$%^&*+-]/) >= 0, 'bg-red-200 text-red-700':password.search(/[!@#$%^&*+-]/) < 0 }" class=" rounded-full p-1 fill-current ">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password.search(/[!@#$%^&*+-]/) >= 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                <path x-show="password.search(/[!@#$%^&*+-]/) < 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span :class="{'text-green-700': password.search(/[!@#$%^&*+-]/) >= 0, 'text-red-700':password.search(/[!@#$%^&*+-]/) < 0 }" class="font-medium text-sm ml-3" x-text="password.search(/[!@#$%^&*+-]/) >= 0 ? 'An special character is reached' : 'At lease one special character required' "></span>
                    </li>
                    <?endif;?>
                </ul>
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


