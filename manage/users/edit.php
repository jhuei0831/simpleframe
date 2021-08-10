<?php
$root = '../../';

include($root . '_config/settings.php');

use _models\framework\database as DB;
use _models\framework\Message as MG;
use _models\framework\Security as SC;
use _models\framework\Toolbox as TB;
use _models\framework\Permission;


if (!Permission::can('users-edit')) {
    MG::flash('Permission Denied!', 'error');
    MG::redirect(APP_ADDRESS . 'manage/users');
}

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
            'name'    => 'required|max_len,30',
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
            $update = DB::table('users')->where("id = '{$id}'")->update($valid_data);
            MG::flash('修改成功，謝謝。', 'success');
            MG::redirect(APP_ADDRESS . 'manage/users');
        } else {
            $profile_error = true;
            MG::flash('修改失敗，請檢查輸入。', 'error');
            // MG::redirect(APP_ADDRESS.'manage/users/edit.php?id='.$id);
        }
    } else {
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
        } elseif ($gump->errors()) {
            $password_error = true;
            MG::flash('修改失敗，請檢查輸入。', 'error');
            // MG::redirect(APP_ADDRESS.'manage/users/edit.php?id='.$id); 
        } else {
            unset($valid_data['password_confirm']);
            $valid_data['password'] = md5($valid_data['password']);
            $update = DB::table('users')->where("id = '{$id}'")->update($valid_data);
            MG::flash('修改成功，謝謝。', 'success');
            MG::redirect(APP_ADDRESS . 'manage/users');
        }
    }
}
include($root . '_layouts/manage/top.php');
?>
<!-- breadcrumb -->
<?php echo TB::breadcrumb(APP_ADDRESS.'manage', ['Users'=> APP_ADDRESS.'manage/users', 'User Edit' => '#'])?>

<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">User Edit</h2>
    <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">Personal Profile</h4>
    <form method="post" id="form_profile">
        <input type="hidden" name="type" value="profile">
        <input type="hidden" name="token" value="<?php echo  TOKEN ?>">
        <?php if (isset($profile_error) && $profile_error) : ?>
            <?php MG::show_flash(); ?>
            <div class="mb-4">
                <?php foreach ($gump->get_readable_errors() as $error_message) : ?>
                    <li>
                        <font color="red"><?php echo  $error_message ?></font>
                    </li>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">Name</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="name" value="<?php echo  isset($_POST['name']) ? $_POST['name'] : $user->name ?>" type="text" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="Jane Doe" required />
                    <div class="mt-2 absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Email</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="email" value="<?php echo  isset($_POST['email']) ? $_POST['email'] : $user->email ?>" type="text" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="example@example.com" required />
                    <div class="mt-2 absolute inset-y-0 right-0 flex items-center mr-3 pointer-events-none">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Role</span>
                <select name="role" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300">
                    <?php foreach ($roles as $role) : ?>
                        <option value="<?php echo  $role->id ?>" <?php echo  $user->role == $role->id ? 'selected' : '' ?>><?php echo  $role->name ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="flex justify-end">
                <button class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    送出
                </button>
            </div>
        </div>
    </form>

    <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">Password</h4>
    <form id="form_password" method="post">
        <input type="hidden" name="type" value="password">
        <input type="hidden" name="token" value="<?php echo  TOKEN ?>">
        <?php if (isset($password_error) && $password_error) : ?>
            <?php MG::show_flash(); ?>
            <div class="mb-4">
                <?php foreach ($gump->get_readable_errors() as $error_message) : ?>
                    <li>
                        <font color="red"><?php echo  $error_message ?></font>
                    </li>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">Password</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="password" type="password" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="*********" />
                </div>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Password Confirm</span>
                <div class="relative text-black focus-within:text-blue-600 dark:focus-within:text-blue-400">
                    <input name="password_confirm" type="password" class="mt-2 w-full focus:ring-indigo-500 focus:border-indigo-500 min-w-0 rounded-md rounded-r-md sm:text-sm border-gray-300" placeholder="*********" />
                </div>
            </label>
            <div class="flex justify-end">
                <button class="px-3 py-1 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    送出
                </button>
            </div>
    </form>
</div>

<?php include($root . '_layouts/manage/bottom.php'); ?>