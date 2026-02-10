@php
$averageRating = $product->average_rating ?? 0;
$totalReviews = $product->total_reviews ?? 0;
@endphp

<div class="group relative bg-white dark:bg-zinc-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="aspect-square overflow-hidden bg-gray-200 dark:bg-zinc-700">
            @if($product->thumbnail)
                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="h-full w-full flex items-center justify-center">
                    <svg class="h-20 w-20 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </div>
    </a>

    {{-- Stock Badge --}}
    @if($product->stock <= 0)
        <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
            Out of Stock
        </div>
    @elseif($product->stock <= 5)
        <div class="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded">
            Only {{ $product->stock }} left
        </div>
    @endif

    <div class="p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $product->category?->name ?? 'Uncategorized' }}</p>
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 hover:text-indigo-600 dark:hover:text-indigo-400">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Rating --}}
        <div class="mt-2 flex items-center">
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="h-4 w-4 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
            </div>
            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $totalReviews }})</span>
        </div>

        <div class="mt-3 flex items-center justify-between">
            <div>
                <p class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($product->selling_price, 2) }}</p>
                @if($product->cost_price && $product->cost_price > $product->selling_price)
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->cost_price, 2) }}</p>
                @endif
            </div>

            @if($product->stock > 0)
                <livewire:cart.add-to-cart :product-id="$product->id" wire:key="add-btn-{{ $product->id }}" />
            @else
                <button disabled class="px-3 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm cursor-not-allowed">
                    Out of Stock
                </button>
            @endif
        </div>
    </div>
</div>
