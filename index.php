<?php
    $root = "./";
    include($root.'_config/settings.php');

    use Kerwin\Core\Support\Facades\Auth;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Session;

    include($root.'_layouts/reception/top.php');
    Message::showFlash();
?>
<?php if (!is_null(Session::get('USER_ID'))): ?>
    <div class="flex justify-center">
        <div class="flex-shrink w-3/4 bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200 mt-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    個人資料
                </h3>
                <p class="max-w-2xl text-sm text-gray-500">
                    註冊時的信箱及名稱.
                </p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="divide-y divide-gray-200">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">
                                名稱
                            </dt>
                            <dd class="mt-1 flex text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span class="flex-grow"><?php echo Auth::user()->name?></span>
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:pt-5">
                            <dt class="text-sm font-medium text-gray-500">
                                信箱
                            </dt>
                            <dd class="mt-1 flex text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span class="flex-grow"><?php echo Auth::user()->email?></span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
<?php endif;?>
<?php include($root.'_layouts/reception/bottom.php') ?>
