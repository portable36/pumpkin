<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} - {{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="{{ Str::limit($product->description, 160) }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-zinc-900">
    @include('partials.navigation')

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('products.index') }}" class="ml-1 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 md:ml-2">Products</a>
                        </div>
                    </li>
                    @if($product->category)
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('categories.show', $product->category->slug) }}" class="ml-1 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 md:ml-2">{{ $product->category->name }}</a>
                        </div>
                    </li>
                    @endif
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-500 md:ml-2" aria-current="page">{{ Str::limit($product->name, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Product Details --}}
            <div class="lg:grid lg:grid-cols-2 lg:gap-8">
                {{-- Product Images --}}
                <div class="mb-8 lg:mb-0">
                    <div class="aspect-square bg-white dark:bg-zinc-800 rounded-lg overflow-hidden">
                        @if($product->thumbnail)
                            <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-zinc-700">
                                <svg class="h-32 w-32 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Product Info --}}
                <div>
                    <div class="mb-4">
                        @if($product->category)
                            <a href="{{ route('categories.show', $product->category->slug) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">{{ $product->category->name }}</a>
                        @endif
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $product->name }}</h1>
                    </div>

                    {{-- Rating --}}
                    <div class="flex items-center mb-4">
                        <div class="flex items-center">
                            @php
                                $averageRating = $product->average_rating ?? 0;
                                $totalReviews = $product->total_reviews ?? 0;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-5 w-5 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ $totalReviews }} reviews)</span>
                    </div>

                    {{-- Price --}}
                    <div class="mb-6">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($product->selling_price, 2) }}</p>
                        @if($product->cost_price && $product->cost_price > $product->selling_price)
                            <p class="text-lg text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->cost_price, 2) }}</p>
                            @php
                                $discount = (($product->cost_price - $product->selling_price) / $product->cost_price) * 100;
                            @endphp
                            <span class="inline-block mt-1 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">Save {{ number_format($discount, 0) }}%</span>
                        @endif
                    </div>

                    {{-- Stock Status --}}
                    <div class="mb-6">
                        @if($product->stock > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                In Stock ({{ $product->stock }} available)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h3>
                        <div class="prose prose-sm text-gray-600 dark:text-gray-400 max-w-none">
                            {{ $product->description ?? 'No description available.' }}
                        </div>
                    </div>

                    {{-- Add to Cart --}}
                    @if($product->stock > 0)
                        <div class="mb-8">
                            <livewire:cart.add-to-cart :product-id="$product->id" />
                        </div>
                    @endif

                    {{-- Additional Info --}}
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">SKU:</span>
                                <span class="text-gray-900 dark:text-white">{{ $product->sku ?? 'N/A' }}</span>
                            </div>
                            @if($product->vendor)
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Vendor:</span>
                                <span class="text-gray-900 dark:text-white">{{ $product->vendor->store_name }}</span>
                            </div>
                            @endif
                            @if($product->weight)
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Weight:</span>
                                <span class="text-gray-900 dark:text-white">{{ $product->weight }} {{ $product->weight_unit }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reviews Section --}}
            @if($product->reviews->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Customer Reviews</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($product->reviews->where('is_approved', true)->take(6) as $review)
                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                <span class="text-indigo-600 dark:text-indigo-400 font-medium">{{ substr($review->user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">{{ $review->comment }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Related Products --}}
            @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related Products</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        @include('partials.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </main>

    @include('partials.footer')
    <livewire:notifications.toast />
    @livewireScripts
</body>
</html>
