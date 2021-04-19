<?php
    use _models\framework\Permission;
?>
<!-- Desktop sidebar -->
<aside class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0">
    <div class="py-4 text-gray-500 dark:text-gray-400">
        <a class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200" href="<?=APP_ADDRESS?>manage/"><?=APP_NAME?></a>
        <ul class="mt-6">
            <?php if(Permission::can('roles-list')): ?>
            <li class="relative px-6 py-3">
                <span
                    <?=(strpos(strtolower($_SERVER['REQUEST_URI']), 'roles') !== false)?'class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"':''?>
                    
                    aria-hidden="true"
                ></span>
                <a
                    class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                    href="<?=APP_ADDRESS?>manage/roles"
                >
                    <i class="bi-person icon"></i>
                    <span class="ml-4">Roles</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(Permission::can('users-list')): ?>
            <li class="relative px-6 py-3">
                <span
                <?=(strpos(strtolower($_SERVER['REQUEST_URI']), 'users') !== false)?'class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"':''?>
                    aria-hidden="true"
                ></span>
                <a
                    class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                    href="<?=APP_ADDRESS?>manage/users"
                >
                    <i class="bi bi-people icon"></i>
                    <span class="ml-4">Users</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <!-- Mobile sidebar -->
    </aside>
    <div
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
    ></div>
    <aside
        class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden"
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0 transform -translate-x-20"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 transform -translate-x-20"
        @click.away="closeSideMenu"
        @keydown.escape="closeSideMenu"
    >
        <div class="py-4 text-gray-500 dark:text-gray-400">
            <a
                class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200"
                href="#"
            >
                Windmill
            </a>
            <ul class="mt-6">
                <?php if(Permission::can('roles-list')): ?>
                <li class="relative px-6 py-3">
                    <span 
                        <?=(strpos(strtolower($_SERVER['REQUEST_URI']), 'roles') !== false)?'class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"':''?> aria-hidden="true"
                    ></span>
                    <a
                        class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                        href="<?=APP_ADDRESS?>manage/roles"
                    >
                        <i class="bi-person icon"></i>
                        <span class="ml-4">Roles</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(Permission::can('users-list')): ?>
                <li class="relative px-6 py-3">
                    <span 
                        <?=(strpos(strtolower($_SERVER['REQUEST_URI']), 'users') !== false)?'class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"':''?>
                        aria-hidden="true"></span>
                    <a
                        class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                        href="<?=APP_ADDRESS?>manage/users"
                    >
                        <i class="bi bi-people icon"></i>
                        <span class="ml-4">Users</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>
    