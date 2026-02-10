<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category->name }} - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-zinc-900">
    @include('partials.navigation')

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('categories.index') }}" class="ml-1 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 md:ml-2">Categories</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-500 md:ml-2" aria-current="page">{{ $category->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Category Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $category->description }}</p>
                @endif
            </div>

            {{-- Subcategories --}}
            @if($category->children->count() > 0)
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subcategories</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($category->children as $child)
                    <a href="{{ route('categories.show', $child->slug) }}" class="px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-lg text-gray-700 dark:text-gray-300 hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        {{ $child->name }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Products Grid --}}
            <div class="mb-6 flex justify-between items-center">
                <p class="text-gray-600 dark:text-gray-400">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-medium">
                    Advanced Search →
                </a>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No products found</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">This category doesn't have any products yet.</p>
                    <a href="{{ route('products.index') }}" class="mt-6 inline-block text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                        Browse all products
                    </a>
                </div>
            @endif
        </div>
    </main>

    @include('partials.footer')
    <livewire:notifications.toast />
    @livewireScripts
</body>
</html>
