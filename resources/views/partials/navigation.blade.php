<nav class="bg-white dark:bg-zinc-800 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ config('app.name') }}</span>
                </a>
                {{-- Desktop Navigation --}}
                <div class="hidden md:ml-8 md:flex md:space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('products.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 text-sm font-medium {{ request()->routeIs('products.*') ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                        Products
                    </a>
                    <a href="{{ route('categories.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 text-sm font-medium {{ request()->routeIs('categories.*') ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                        Categories
                    </a>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="flex-1 flex items-center justify-center px-8 max-w-2xl">
                <form action="{{ route('products.index') }}" method="GET" class="w-full">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 pl-4 pr-10 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 dark:placeholder-gray-400">
                        <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right Side Actions --}}
            <div class="flex items-center space-x-4">
                {{-- Cart --}}
                <livewire:cart.minicart />

                {{-- Auth Buttons --}}
                @guest
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm font-medium">Sign in</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">Sign up</a>
                    </div>
                @else
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm font-medium">
                            {{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm font-medium">Logout</button>
                        </form>
                    </div>
                @endguest

                {{-- Mobile menu button --}}
                <button type="button" class="md:hidden p-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-zinc-700 {{ request()->routeIs('home') ? 'text-indigo-600 dark:text-indigo-400 bg-gray-50 dark:bg-zinc-700' : '' }}">
                Home
            </a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-zinc-700 {{ request()->routeIs('products.*') ? 'text-indigo-600 dark:text-indigo-400 bg-gray-50 dark:bg-zinc-700' : '' }}">
                Products
            </a>
            <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-zinc-700 {{ request()->routeIs('categories.*') ? 'text-indigo-600 dark:text-indigo-400 bg-gray-50 dark:bg-zinc-700' : '' }}">
                Categories
            </a>
            <a href="{{ route('cart.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-zinc-700">
                Cart
            </a>
        </div>
        @guest
            <div class="pt-4 pb-2 border-t border-gray-200 dark:border-zinc-700">
                <div class="px-4 space-y-2">
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Sign in</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium bg-indigo-600 text-white text-center hover:bg-indigo-700">Sign up</a>
                </div>
            </div>
        @endguest
    </div>
</nav>
