<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-zinc-900">
    @include('partials.navigation')

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">All Products</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Browse our collection of quality products</p>
            </div>

            <livewire:search.product-search />
        </div>
    </main>

    @include('partials.footer')
    <livewire:notifications.toast />
    @livewireScripts
</body>
</html>
