<div class="min-h-screen bg-gray-50 dark:bg-zinc-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Shopping Cart</h1>

        @if(count($items) > 0)
            <div class="lg:grid lg:grid-cols-12 lg:gap-12">
                {{-- Cart Items --}}
                <div class="lg:col-span-8">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm">
                        <ul class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @foreach($items as $item)
                            <li class="p-6 flex items-center">
                                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md bg-gray-200 dark:bg-zinc-700">
                                    @if($item->product->thumbnail)
                                        <img src="{{ Storage::url($item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="ml-6 flex-1">
                                    <div class="flex justify-between">
                                        <div>
                                            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->product->category?->name ?? 'Uncategorized' }}</p>
                                        </div>
                                        <p class="text-base font-medium text-gray-900 dark:text-white">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center border border-gray-300 dark:border-zinc-600 rounded-lg">
                                            <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="px-3 py-1 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-l-lg">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="px-4 py-1 text-sm font-medium text-gray-900 dark:text-white min-w-[3rem] text-center">{{ $item->quantity }}</span>
                                            <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="px-3 py-1 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-r-lg">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>

                                        <button wire:click="removeItem({{ $item->id }})" class="text-red-500 hover:text-red-700 text-sm font-medium flex items-center">
                                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>

                        <div class="p-6 border-t border-gray-200 dark:border-zinc-700">
                            <button wire:click="clearCart" class="text-red-500 hover:text-red-700 text-sm font-medium">
                                Clear Cart
                            </button>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-medium flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Continue Shopping
                        </a>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-4 mt-8 lg:mt-0">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Summary</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal ({{ $totalItems }} items)</span>
                                <span class="text-gray-900 dark:text-white font-medium">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                <span class="text-gray-900 dark:text-white font-medium">Calculated at checkout</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="text-gray-900 dark:text-white font-medium">Calculated at checkout</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between text-base font-semibold">
                                <span class="text-gray-900 dark:text-white">Estimated Total</span>
                                <span class="text-gray-900 dark:text-white">${{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>

                        <button class="mt-6 w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm py-16">
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">Your cart is empty</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Looks like you haven't added anything to your cart yet.</p>
                    <a href="{{ route('products.index') }}" class="mt-6 inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700">
                        Start Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
