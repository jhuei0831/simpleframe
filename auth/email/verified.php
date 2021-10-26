<?php
    $root = '../../';
    $pageTitle = 'Email varified';
    include_once($root.'_config/settings.php');

    use _models\Auth\Email;
    use Kerwin\Core\Support\Facades\Auth;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Session;

    if (is_null(Session::get('USER_ID')) || !empty(Auth::user()->email_varified_at) || EMAIL_VERIFY==='FALSE') {
        include_once($root.'_error/404.php');
        exit;
    }

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $mail = Email::getInstance();
        $mail->getVerifyEmail($_POST);
    }
    
    Message::showFlash();
    include_once($root.'_layouts/auth/top.php');
?>
<div class="flex items-center justify-center bg-gray-50 py-32 px-4 sm:px-6 lg:px-8" x-data={loading:false}>
    <div class="max-w-md w-full space-y-8 mt-12">
        <div>
            <a href="<?php echo APP_ADDRESS?>">
                <img :class="{ 'animate-spin': loading === true }" class="mx-auto h-12 w-auto" src="<?php echo APP_IMG?>grapes.png" alt="<?php echo APP_NAME ?>">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                電子信箱驗證
            </h2>
        </div>
        <form action="./verified.php" class="mt-8 space-y-6" method="POST" @submit="loading = true">
            <input type="hidden" name="token" value="<?php echo TOKEN?>">
            <div class="rounded-md shadow-sm -space-y-px text-center">
                註冊成功，請至註冊填寫的電子信箱中收取驗證信，如果沒有收到認證信，請點下方按鈕獲取，謝謝。
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                        </svg>
                    </span>
                    獲取信箱驗證信
                </button>
                <a href="<?php echo APP_ADDRESS?>auth/logout.php" class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" role="menuitem">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    登出
                </a>
            </div>
        </form>
    </div>
</div>
<?php include_once($root.'_layouts/auth/bottom.php'); ?>
