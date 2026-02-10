<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button @click="open = !open" class="relative p-2 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @if($totalItems > 0)
            <span class="absolute -top-1 -right-1 bg-indigo-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $totalItems > 99 ? '99+' : $totalItems }}
            </span>
        @endif
    </button>

    {{-- Mini Cart Dropdown --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Shopping Cart</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if(count($items) > 0)
                <div class="space-y-4 max-h-64 overflow-y-auto">
                    @foreach($items as $item)
                    <div class="flex items-center space-x-3">
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md bg-gray-200 dark:bg-zinc-700">
                            @if($item->product->thumbnail)
                                <img src="{{ Storage::url($item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                        </div>
                        <button wire:click="removeItem({{ $item->id }})" class="text-red-500 hover:text-red-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-base font-medium text-gray-900 dark:text-white">Subtotal</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('cart.index') }}" class="block w-full bg-indigo-600 text-white text-center px-4 py-2 rounded-lg hover:bg-indigo-700">
                            View Cart
                        </a>
                        <button @click="open = false" class="block w-full bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200 text-center px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-zinc-600">
                            Continue Shopping
                        </button>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Your cart is empty</p>
                    <a href="{{ route('products.index') }}" @click="open = false" class="mt-4 inline-block text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
