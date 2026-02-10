<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Online Shopping</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-zinc-900">
    @include('partials.navigation')

    <main>
        {{-- Hero Section --}}
        <section class="relative bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-800 dark:to-purple-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Welcome to {{ config('app.name') }}
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-indigo-100 max-w-2xl mx-auto">
                        Discover amazing products from trusted vendors. Shop the latest trends with secure payments and fast delivery.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="{{ route('products.index') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Shop Now
                        </a>
                        <a href="{{ route('categories.index') }}" class="text-sm font-semibold leading-6 text-white">
                            Browse Categories <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Categories Section --}}
        @if($categories->count() > 0)
        <section class="py-12 bg-white dark:bg-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Shop by Category</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="group">
                        <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-700">
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="h-full w-full flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
                        </div>
                        <h3 class="mt-3 text-sm font-medium text-gray-900 dark:text-white text-center">{{ $category->name }}</h3>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- Featured Products Section --}}
        @if($featuredProducts->count() > 0)
        <section class="py-12 bg-gray-50 dark:bg-zinc-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Featured Products</h2>
                    <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-medium">
                        View all <span aria-hidden="true">→</span>
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- New Arrivals Section --}}
        @if($newArrivals->count() > 0)
        <section class="py-12 bg-white dark:bg-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">New Arrivals</h2>
                    <a href="{{ route('products.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-medium">
                        View all <span aria-hidden="true">→</span>
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($newArrivals as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- Features Section --}}
        <section class="py-12 bg-gray-50 dark:bg-zinc-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 text-indigo-600 dark:text-indigo-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Free Shipping</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">On orders over $50</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 text-indigo-600 dark:text-indigo-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Secure Payment</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">100% secure checkout</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 text-indigo-600 dark:text-indigo-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Easy Returns</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">30-day return policy</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 text-indigo-600 dark:text-indigo-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">24/7 Support</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Always here to help</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.footer')
    <livewire:notifications.toast />
    @livewireScripts
</body>
</html>
