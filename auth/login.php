<?php
    $root = '../';

    include_once($root.'_config/settings.php');

    use _models\framework\Security as SC;
    use _models\framework\Database as DB;
    use _models\framework\Message as MG;

    if (!is_null($_SESSION['USER_ID'])) {
        MG::redirect(APP_ADDRESS);
    }
    
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $data = SC::defend_filter($_POST);
        $user = DB::table('users')->where('email ="'.$data['email'].'" and password ="'.md5($data['password']).'"')->first();
        if ($user && empty($user->email_varified_at)) {
            $_SESSION['USER_ID'] = $user->id;
            MG::flash('登入成功，尚未完成信箱驗證', 'warning');
            MG::redirect(APP_ADDRESS.'auth/email/verified.php');
        }
        elseif ($user) {
            $_SESSION['USER_ID'] = $user->id;
            MG::flash('登入成功', 'success');
            MG::redirect(APP_ADDRESS);
        }
        else {
            MG::flash('登入失敗', 'error');
        }
    }
    MG::show_flash();
    include_once($root.'_layouts/auth/top.php');
?>
<div class="flex h-full items-center justify-center bg-gray-50 pb-32 px-4 sm:px-6 lg:px-8" x-data={loading:false}>
    <div class="max-w-md w-full space-y-8 mt-12">
        <div>
            <a href="<?=APP_ADDRESS?>">
                <img :class="{ 'animate-spin': loading === true }" class="mx-auto h-12 w-auto" src="<?=APP_IMG?>grapes.png" alt="Workflow">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
        </div>
        <form class="mt-8 space-y-6" method="POST" @submit="loading = true">
            <input type="hidden" name="token" value="<?=TOKEN?>">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" value="<?=isset($data['email'])?$data['email']:''?>" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
            </div>

            <div class="justify-center">
                <a href="<?=APP_ADDRESS?>auth/password<?=PASSWORD_SECURE==='TRUE'?'_secure':''?>/password_forgot.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Forgot your password?
                </a>
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" >
                            <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </span>
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
<?php include_once($root.'_layouts/auth/bottom.php'); ?>
