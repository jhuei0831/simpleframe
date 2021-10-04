<?php
    $root = '../';

    include_once($root . '_config/settings.php');

    use _models\Auth\User;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Session;

    if (!is_null(Session::get('USER_ID'))) {
        Message::redirect(APP_ADDRESS);
    }

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $user = new User();
        $login = $user->login($_POST);
        $user->result($login);
    }

    Message::showFlash();
    include_once($root . '_layouts/auth/top.php');
?>
<script>
    function refresh_code() {
        document.getElementById("imgcode").src = "<?php echo APP_URL ?>_partials/captcha.php?" + Date.now();
    }
</script>
<div class="flex h-full items-center justify-center bg-gray-50 pb-32 px-4 sm:px-6 lg:px-8" x-data={loading:false}>
    <div class="max-w-md w-full space-y-8 mt-12">
        <div>
            <a href="<?php echo APP_ADDRESS ?>">
                <img :class="{ 'animate-spin': loading === true }" class="mx-auto h-12 w-auto" src="<?php echo APP_IMG ?>grapes.png" alt="Workflow">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                會員登入
            </h2>
        </div>
        <form class="mt-8 space-y-6" method="POST" @submit="loading = true">
            <input type="hidden" name="token" value="<?php echo TOKEN ?>">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">電子郵件</label>
                    <input id="email" name="email" type="email" value="<?php echo isset($data['email']) ? $data['email'] : '' ?>" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="電子郵件">
                </div>
                <div>
                    <label for="password" class="sr-only">密碼</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="密碼">
                </div>
            </div>

            <div>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                        <img id="imgcode" src="<?php echo APP_URL ?>_partials/captcha.php" onclick="refresh_code()" />
                    </span>
                    <input type="text" name="captcha" id="captcha" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300" autocomplete="off" required placeholder="請輸入左方圖片驗證碼">
                </div>
                <p class="mt-2 text-sm text-gray-500" id="email-description">可以點擊圖片更換驗證碼</p>
            </div>

            <div class="justify-center">
                <a href="<?php echo APP_ADDRESS ?>auth/password/password_forgot.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    忘記密碼?
                </a>
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </span>
                    登入
                </button>
            </div>
        </form>
    </div>
</div>
<?php include_once($root . '_layouts/auth/bottom.php'); ?>