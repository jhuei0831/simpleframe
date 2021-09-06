<?php
    $root = "./";
    include($root.'_config/settings.php');

    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Auth;

    include($root.'_layouts/reception/top.php');
    Message::showFlash();
?>
<?php if (Auth::user()): ?>
    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200 mt-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Profile
            </h3>
            <p class="max-w-2xl text-sm text-gray-500">
                This information will be displayed publicly so be careful what you share.
            </p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="divide-y divide-gray-200">
                <dl class="divide-y divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Name
                        </dt>
                        <dd class="mt-1 flex text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="flex-grow"><?php echo Auth::user()->name?></span>
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:pt-5">
                        <dt class="text-sm font-medium text-gray-500">
                            Email
                        </dt>
                        <dd class="mt-1 flex text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="flex-grow"><?php echo Auth::user()->email?></span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
<?php endif;?>
<?php include($root.'_layouts/reception/bottom.php') ?>
