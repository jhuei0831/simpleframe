<?
    use _models\Permission;
?>
<div class="flex flex-col flex-1 w-full" x-data="{ open: false, menu: false }">
    <header class="z-10 dark:bg-gray-800">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex justify-between items-center border-b-2 border-gray-100 py-6 md:justify-start md:space-x-10">
                    <div class="flex justify-start">
                        <a href="#">
                            <span class="sr-only">icon</span>
                            <img class="h-8 w-auto sm:h-10" src="<?=APP_IMG?>grapes.png" alt="icon">
                        </a>
                    </div>
                    <div class="-mr-2 -my-2 md:hidden">
                        <button @click="menu = true" type="button" class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-expanded="false">
                            <span class="sr-only">Open menu</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                    <nav class="hidden md:block space-x-10">
                        <a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Pricing</a>
                        <a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Docs</a>
                    </nav>
                    <div class="hidden md:block items-center justify-end">
                    <?php if(is_null($_SESSION['USER_ID'])): ?>
                        <a href="<?=APP_ADDRESS?>auth/login.php" class="whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900">Sign in</a>
                        <a href="<?=APP_ADDRESS?>auth/register.php" class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">Sign up</a>
                    <?php else: ?>
                        <div class="relative inline-block text-left">
                            <!-- 選單按鈕 -->
                            <div class="cursor-pointer">
                                <span @click="open = true" class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-500">
                                    <span class="font-medium leading-none text-white">TW</span>
                                </span>
                            </div>

                            <!-- 下拉選單動畫 -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                            ></div>
                            <!-- 選單內容 -->
                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <? if(Permission::can('manage-read')): ?>
                                <div class="py-1 flex" role="none">
                                    <a href="<?=APP_ADDRESS?>manage/" class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" role="menuitem">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                        Backstage
                                    </a>
                                </div>
                                <? endif; ?>
                                <div class="py-1 flex" role="none">
                                    <a href="<?=APP_ADDRESS?>auth/logout.php" class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" role="menuitem">
                                        <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" >
                                            <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        Sign out
                                    </a>
                                </div>
                            </div>
                        </div>
                    <? endif;?>
                    </div>
                </div>
            </div>

            <div
                x-show="menu"
                x-transition:enter="ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            ></div>
            <div x-show="menu" @click.away="menu = false" class="absolute top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden">
                <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y-2 divide-gray-50">
                    <div class="pt-5 pb-6 px-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <img class="h-8 w-auto" src="<?=APP_IMG?>grapes.png" alt="icon">
                            </div>
                            <div class="-mr-2">
                                <button @click="menu = false" type="button" class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                    <span class="sr-only">Close menu</span>
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-6">
                            <nav class="grid gap-y-8">
                                <a href="#" class="-m-3 p-3 flex items-center rounded-md hover:bg-gray-50">
                                    <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <span class="ml-3 text-base font-medium text-gray-900">
                                        Analytics
                                    </span>
                                </a>
                            </nav>
                        </div>
                    </div>
                    <div class="py-6 px-5 space-y-6">
                        <div>
                            <?php if(is_null($_SESSION['USER_ID'])): ?>
                            <a href="<?=APP_ADDRESS?>auth/register.php" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Sign up
                            </a>
                            <p class="mt-6 text-center text-base font-medium text-gray-500">
                                Existing customer?
                                <a href="<?=APP_ADDRESS?>auth/login.php" class="text-indigo-600 hover:text-indigo-500">Sign in</a>
                            </p>
                            <?php else: ?>
                                <a href="<?=APP_ADDRESS?>auth/logout.php" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" >
                                        <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign out
                                </a>
                            <? endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>