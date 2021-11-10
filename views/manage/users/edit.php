<?php
    $root = '../../';

    include($root . 'config/settings.php');

    use models\Auth\User;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;
    use Kerwin\Core\Support\Facades\Permission;

    if (!Permission::can('users-edit')) {
        Message::flash('權限不足!', 'error')->redirect(APP_ADDRESS . 'manage/users');
    }

    $id = Security::defendFilter($_GET['id']);
    $user = Database::table('users')->find($id);
    $roles = Database::table('roles')->get();

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $userInstance = new User();
        $userInstance->edit($_POST, $id);
        $errors = $userInstance->errors;
    }
    
    Message::showFlash();
    include($root . '_layouts/manage/top.php');
?>
<!-- breadcrumb -->
<?php echo Toolbox::breadcrumb(APP_ADDRESS.'manage', ['使用者管理'=> APP_ADDRESS.'manage/users', '編輯使用者' => '#'])?>

<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">編輯使用者</h2>
    <div class="mb-4">
        <?php include_once($root.'_partials/error_message.php'); ?>
    </div>
    <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">個人資料</h4>
    <form method="post" id="form_profile">
        <input type="hidden" name="type" value="profile">
        <input type="hidden" name="token" value="<?php echo TOKEN ?>">
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">名稱</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="name" value="<?php echo isset($_POST['name']) ? Security::defendFilter($_POST['name']) : $user->name ?>" type="text" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="Jane Doe" required />
                    <div class="mt-2 absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">電子郵件</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="email" value="<?php echo isset($_POST['email']) ? Security::defendFilter($_POST['email']) : $user->email ?>" type="text" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="example@example.com" required />
                    <div class="mt-2 absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">角色</span>
                <select name="role" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300">
                    <?php foreach ($roles as $role) : ?>
                        <option value="<?php echo $role->id ?>" <?php echo $user->role == $role->id ? 'selected' : '' ?>><?php echo $role->name ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="flex justify-end">
                <button class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    送出
                </button>
            </div>
        </div>
    </form>

    <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">密碼重設</h4>
    <form id="form_password" method="post">
        <input type="hidden" name="type" value="password">
        <input type="hidden" name="token" value="<?php echo TOKEN ?>">
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">密碼</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="password" autocomplete="new-password" type="password" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="*********" />
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">確認密碼</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="password_confirm" autocomplete="new-password" type="password" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="*********" />
                </div>
            </label>
            <div class="flex justify-end">
                <button class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    送出
                </button>
            </div>
        </div>
    </form>
</div>

<?php include($root . '_layouts/manage/bottom.php'); ?>