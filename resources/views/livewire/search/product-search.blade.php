<div class="lg:grid lg:grid-cols-4 lg:gap-8">
    {{-- Filters Sidebar --}}
    <div class="hidden lg:block">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6 sticky top-24">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filters</h2>
                <button wire:click="clearFilters" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                    Clear all
                </button>
            </div>

            {{-- Search Filter --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search products..." class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Categories --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($categories as $cat)
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" wire:model.live="category" value="{{ $cat->slug }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $cat->name }} ({{ $cat->products_count }})</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Price Range --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price Range</label>
                <div class="flex items-center space-x-2">
                    <input type="number" wire:model.live="minPrice" placeholder="Min" class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="text-gray-500">-</span>
                    <input type="number" wire:model.live="maxPrice" placeholder="Max" class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Stock Filter --}}
            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="inStock" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">In Stock Only</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Products Grid --}}
    <div class="lg:col-span-3">
        {{-- Mobile Filters & Sort --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            {{-- Mobile Filter Button --}}
            <button type="button" class="lg:hidden flex items-center px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-lg text-gray-700 dark:text-gray-300" onclick="document.getElementById('mobile-filters').classList.toggle('hidden')">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </button>

            {{-- Sort Dropdown --}}
            <div class="flex items-center w-full sm:w-auto">
                <label class="text-sm text-gray-600 dark:text-gray-400 mr-2">Sort by:</label>
                <select wire:model.live="sort" class="rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="latest">Newest First</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="name">Name: A to Z</option>
                    <option value="popular">Most Popular</option>
                </select>
            </div>
        </div>

        {{-- Mobile Filters Panel --}}
        <div id="mobile-filters" class="hidden lg:hidden mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-4">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search products..." class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select wire:model.live="category" class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price Range</label>
                    <div class="flex items-center space-x-2">
                        <input type="number" wire:model.live="minPrice" placeholder="Min" class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="text-gray-500">-</span>
                        <input type="number" wire:model.live="maxPrice" placeholder="Max" class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 py-2 px-3 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="inStock" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">In Stock Only</span>
                </label>

                <button wire:click="clearFilters" class="w-full px-4 py-2 bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-600">
                    Clear Filters
                </button>
            </div>
        </div>

        {{-- Results Count --}}
        <p class="text-gray-600 dark:text-gray-400 mb-4">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>

        {{-- Products Grid --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No products found</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Try adjusting your search or filters to find what you're looking for.</p>
                <button wire:click="clearFilters" class="mt-6 inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700">
                    Clear Filters
                </button>
            </div>
        @endif
    </div>
</div>
