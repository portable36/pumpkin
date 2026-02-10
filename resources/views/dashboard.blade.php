<x-layouts::app :title="__('Customer Dashboard')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Welcome Section --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Here's what's happening with your account</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Orders Stat --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->orders?->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Cart Stat --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cart Items</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            @php
                                $cart = app(\App\Services\Cart\CartService::class)->getCart();
                            @endphp
                            {{ $cart?->total_items ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Wishlist Stat --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Wishlist</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->wishlists?->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Account Stat --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Member Since</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Recent Orders --}}
            <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
                </div>
                <div class="p-6">
                    @php
                        $recentOrders = auth()->user()->orders?->take(5) ?? collect();
                    @endphp

                    @if($recentOrders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order #</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">#{{ $order->order_number }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($order->status === 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${{ number_format($order->grand_total ?? 0, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No orders yet</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Links</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('products.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Browse Products</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Find your next purchase</p>
                        </div>
                    </a>

                    <a href="{{ route('cart.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">My Cart</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View and manage items</p>
                        </div>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">My Profile</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Update your details</p>
                        </div>
                    </a>

                    <a href="{{ route('settings.profile') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                        <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <svg class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Settings</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage preferences</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
