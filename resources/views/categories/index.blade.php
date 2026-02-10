<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categories - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-zinc-900">
    @include('partials.navigation')

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Categories</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Browse products by category</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <a href="{{ route('categories.show', $category->slug) }}">
                        <div class="aspect-video bg-gray-200 dark:bg-zinc-700">
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </a>
                    <div class="p-6">
                        <a href="{{ route('categories.show', $category->slug) }}">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">{{ $category->name }}</h2>
                        </a>
                        <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">{{ Str::limit($category->description, 100) }}</p>

                        @if($category->children->count() > 0)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($category->children as $child)
                            <a href="{{ route('categories.show', $child->slug) }}" class="text-xs px-2 py-1 bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-zinc-600">
                                {{ $child->name }}
                            </a>
                            @endforeach
                        </div>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700 flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $category->products_count ?? $category->products()->count() }} products</span>
                            <a href="{{ route('categories.show', $category->slug) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 text-sm font-medium">
                                Explore →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </main>

    @include('partials.footer')
    <livewire:notifications.toast />
    @livewireScripts
</body>
</html>
