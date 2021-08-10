<div class="flex-1 overflow-auto focus:outline-none">
    <div class="relative z-10 flex-shrink-0 flex h-16 bg-white border-b border-gray-200 lg:border-none">
        <button class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-cyan-500 lg:hidden" @click="sidebar = true">
            <span class="sr-only">Open sidebar</span>
            <svg class="h-6 w-6" x-description="Heroicon name: outline/menu-alt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
            </svg>
        </button>
        <!-- Search bar -->
        <div class="flex-1 px-4 flex justify-between sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
            <div class="flex-1 flex">
                <!-- Search Form -->
                <!-- <form class="w-full flex md:ml-0" action="#" method="GET">
                    <label for="search_field" class="sr-only">Search</label>
                    <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                        <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none" aria-hidden="true">
                            <svg class="h-5 w-5" x-description="Heroicon name: solid/search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input id="search_field" name="search_field" class="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-0 focus:border-transparent sm:text-sm" placeholder="Search transactions" type="search">
                    </div>
                </form> -->
            </div>
            <div class="ml-4 flex items-center md:ml-6">
                <!-- Profile dropdown -->
                <div class="ml-3 relative" @click.away="profile = false">
                    <div>
                        <button 
                            type="button" 
                            class="max-w-xs bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 lg:p-2 lg:rounded-md lg:hover:bg-gray-50" 
                            id="user-menu-button" 
                            x-ref="button" 
                            @click="profile = true" 
                            @keydown.escape="profile = false"
                            aria-expanded="false" 
                            aria-haspopup="true" 
                        >
                            <img class="h-8 w-8 rounded-full" src="https://uybor.uz/borless/avtobor/img/user-images/no-avatar.png" alt="avatar">
                            <span class="hidden ml-3 text-gray-700 text-sm font-medium lg:block">
                                <span class="sr-only">Open user menu for </span>
                                <?php echo _models\framework\Auth::user()->name?>
                            </span>
                            <svg class="hidden flex-shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <div 
                        x-show="profile" 
                        x-transition:enter="transition ease-out duration-100" 
                        x-transition:enter-start="transform opacity-0 scale-95" 
                        x-transition:enter-end="transform opacity-100 scale-100" 
                        x-transition:leave="transition ease-in duration-75" 
                        x-transition:leave-start="transform opacity-100 scale-100" 
                        x-transition:leave-end="transform opacity-0 scale-95" 
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                        x-ref="menu-items" 
                        role="menu" 
                        aria-orientation="vertical" 
                        aria-labelledby="user-menu-button" 
                        tabindex="-1" 
                        style="display: none;"
                    >
                        <a 
                            href="<?php echo APP_ADDRESS?>" 
                            class="flex items-center px-4 py-2 text-sm text-gray-700" 
                            :class="{ 'bg-gray-100': activeIndex === 0 }" 
                            role="menuitem" 
                            tabindex="-1" 
                            id="user-menu-item-0" 
                            @mouseenter="activeIndex = 0" 
                            @mouseleave="activeIndex = -1" 
                            @click="profile = false"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                            </svg>
                            Your Profile
                        </a>
                        <a 
                            href="#" 
                            class="flex items-center px-4 py-2 text-sm text-gray-700" 
                            :class="{ 'bg-gray-100': activeIndex === 1 }" 
                            role="menuitem" 
                            tabindex="-1" 
                            id="user-menu-item-1" 
                            @mouseenter="activeIndex = 1" 
                            @mouseleave="activeIndex = -1" 
                            @click="profile = false"
                        >
                            <svg class="w-5 h-5 mr-3" x-description="Heroicon name: outline/cog" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                        <a 
                            href="<?php echo APP_ADDRESS?>auth/logout.php" 
                            class="flex items-center px-4 py-2 text-sm text-gray-700" 
                            :class="{ 'bg-gray-100': activeIndex === 2 }" 
                            role="menuitem" 
                            tabindex="-1" 
                            id="user-menu-item-2"
                            @mouseenter="activeIndex = 2" 
                            @mouseleave="activeIndex = -1" 
                            @click="profile = false"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Sign out
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>