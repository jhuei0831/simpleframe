<?php
    $root = '../../';
    $page_title = 'Password Reset';
    include_once($root.'_config/settings.php');

    use _models\framework\Security as SC;
    use _models\framework\Database as DB;
    use _models\framework\Message as MG;
    use _models\framework\Mail;

    // 已登入不能訪問此頁面
    if (!is_null($_SESSION['USER_ID'])) {
        include_once($root.'_error/404.php');
        exit;
    }

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = SC::defend_filter($_POST);
        $auth_code = SC::defend_filter(uniqid(mt_rand()));
        $user = DB::table('users')->where("email = '{$data['email']}'")->first();
        // 放到信中的變數
        $name = $user->name;
        $id = $user->id;
        include_once('./content.php');
        $mail = Mail::send($subject, $message, $user->email, $user->name);
        $password_resets = DB::table('password_resets')->where("id = '{$user->id}'")->first(false);
        if ($mail) {
            DB::table('password_resets')->CreateOrUpdate([
                'token' => $data['token'], 
                'id' => $id, 
                'password' => isset($password_resets->password) ? $password_resets->password : json_encode([$user->password]),
                'email_token' => $auth_code, 
                'token_updated_at' => date('Y-m-d H:i:s'), 
                'password_updated_at' => isset($password_resets->password_updated_at) ? $password_resets->password_updated_at : $user->created_at, 
            ]);
            MG::flash('請前往註冊信箱收取密碼重設信，謝謝。', 'success');
            MG::redirect(APP_ADDRESS.'auth/password/password_forgot.php');
        }
        else{
            MG::flash('獲取信件失敗', 'error');
            MG::redirect(APP_ADDRESS.'auth/password/password_forgot.php');
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
                Forgot Password
            </h2>
        </div>
        <form class="mt-8 space-y-6" method="POST" @submit="loading = true">
            <input type="hidden" name="token" value="<?=TOKEN?>">
            <div class="rounded-md shadow-sm -space-y-px text-center">
                輸入註冊信箱並按下送出按鈕獲取密碼重設信件，謝謝。
            </div>
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" value="<?=isset($data['email'])?$data['email']:''?>" autocomplete="email" required class="rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                </div>
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </span>
                    送出
                </button>
            </div>
        </form>
    </div>
</div>
<?php include_once($root.'_layouts/auth/bottom.php'); ?>

