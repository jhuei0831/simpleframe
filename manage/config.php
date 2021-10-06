<?php

    $root = '../';
    $pageTitle = '設定';

    include($root . '_config/settings.php');

    use _models\Config;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Permission;
    
    $configController = new Config();
    $config = $configController->index();
    
    if (!Permission::can('config-edit')) {
        include_once($root . '_error/404.php');
        exit;
    }

    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $configController->edit($_POST, $config->id);
    }
    
    Message::showFlash();
    include($root . '_layouts/manage/top.php');
?>
<!-- breadcrumb -->
<?php echo Toolbox::breadcrumb(APP_ADDRESS . 'manage', ['網站設定' => '#']) ?>

<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">設定</h2>
    <div class="w-full mb-8 overflow-hidden rounded-lg shadow">
        <div class="w-full overflow-x-auto py-12 px-4 sm:px-6 lg:px-8 bg-white">
            <form class="space-y-8 divide-y divide-gray-200" method="POST">
                <div class="space-y-8 sm:space-y-5">
                    <div>
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                系統設定
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                前台設定
                            </p>
                        </div>

                        <div class="mt-6 sm:mt-5 space-y-6 sm:space-y-5">
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                                <label for="username" class="block text-sm text-gray-700 sm:mt-px sm:pt-2">
                                    網站狀態
                                </label>
                                <div class="mt-1 sm:mt-0 sm:col-span-2">
                                    <label for="toogleButton" class="flex items-center cursor-pointer mt-2">
                                        <div class="relative">
                                            <input id="toogleButton" name="isOpen" type="checkbox" class="hidden" <?php echo ($config->isOpen==1?'checked':'') ?> />
                                            <div class="toggle-path bg-gray-200 w-9 h-5 rounded-full shadow-inner"></div>
                                            <div class="toggle-circle absolute w-3.5 h-3.5 bg-white rounded-full shadow inset-y-0 left-0"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="token" value="<?php echo TOKEN ?>">
                <div class="pt-5">
                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            保存
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<?php include($root . '_layouts/manage/bottom.php') ?>
