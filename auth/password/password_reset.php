<?php
    $root = '../../';
    $page_title = '密碼重新設定';
    include_once($root.'_config/settings.php');

    use _models\Auth\Password;
    use Kerwin\Core\Support\Facades\Message;

    $passwordController = new Password();
    $passwordController->passwordResetVerify();

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $passwordController->passwordReset($_POST);
    }
    Message::showFlash();
    include_once($root.'_layouts/auth/top.php');
?>
<div class="flex h-full items-center justify-center bg-gray-50 pb-32 px-4 sm:px-6 lg:px-8" x-data="{loading: false, password: '', password_confirm: ''}">
    <div class="max-w-md w-full space-y-8 mt-12">
        <div>
            <a href="<?php echo APP_ADDRESS?>">
                <img :class="{'animate-spin': loading === true}" class="mx-auto h-12 w-auto" src="<?php echo APP_IMG?>grapes.png" alt="Workflow">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                密碼重新設定
            </h2>
        </div>
        <form class="mt-8 space-y-6" method="POST" id="form_reset" @submit="loading = true">
            <input type="hidden" name="token" value="<?php echo TOKEN?>">
            <div class="rounded-md shadow-sm -space-y-px text-center">
                請輸入新的密碼，謝謝。
            </div>

            <div class="mb-4">
                <?php include_once($root.'_partials/error_message.php'); ?>
            </div> 
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="password" class="sr-only">密碼</label>
                    <input id="password" name="password" x-model="password" type="password" autocomplete="current-password" required class="appearance-none rounded-t-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="密碼">
                </div>
                <div>
                    <label for="password_confirm" class="sr-only">確認密碼</label>
                    <input id="password_confirm" name="password_confirm" x-model="password_confirm" type="password" autocomplete="confirm-password" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="確認密碼">
                </div>
            </div>

            <div class="flex justify-start mt-3 ml-4 p-1">
                <ul>
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

