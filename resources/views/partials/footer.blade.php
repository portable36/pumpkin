<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Company Info --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ config('app.name') }}</h3>
                <p class="text-gray-400 text-sm">Your one-stop destination for quality products from trusted vendors. Shop with confidence.</p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white">Products</a></li>
                    <li><a href="{{ route('categories.index') }}" class="hover:text-white">Categories</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:text-white">Cart</a></li>
                </ul>
            </div>

            {{-- Customer Service --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Customer Service</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-white">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white">FAQs</a></li>
                    <li><a href="#" class="hover:text-white">Shipping Info</a></li>
                    <li><a href="#" class="hover:text-white">Returns</a></li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Newsletter</h3>
                <p class="text-gray-400 text-sm mb-4">Subscribe to get updates on new products and offers.</p>
                <form class="flex">
                    <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-2 rounded-l-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</footer>
