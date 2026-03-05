@extends('site.layouts.app')
@php
    // $shop get from controller
    $vendor = App\Models\Vendor::with('reviews', 'shops')->where('id', $shop->vendor_id)->first();
    $reviewCount = $vendor->reviews->where('status', 'Active')->count();
    $avg = $vendor->reviews->where('status', 'Active')->avg('rating');
    $positiveRating = App\Models\Product::positiveRating($shop->vendor_id);
    $reviews = $vendor->reviews()->where('reviews.status', 'Active')->orderBy('created_at', 'desc')->with('user')->paginate(5);
    $progessBarRating = $vendor->reviews()->where('reviews.status', 'Active')->select(\DB::raw('count("rating") as total_rating, rating'))->groupBy('rating')->orderBy('rating', 'desc')->get()->toArray();
    $topSellerIds = App\Models\Vendor::topSeller()->pluck('vendor_id')->toArray();

    $homeService = $homeService = new \Modules\CMS\Service\HomepageService();
    $vendorPage = $homeService->home($shop->vendor_id);
@endphp
@section('page_title', $vendor->name)
@section('seo')
    @include('site.shop.seo', ['page' => $vendorPage])
@endsection
@section('content')
    <section class="layout-wrapper px-4 xl:px-0 ">
        {{-- profile and top benner --}}
        @include('site.shop.top-banner')
        {{-- menu items and search --}}
        @include('site.shop.menu')
        {{-- third section --}}
        <div class="mt-11 rounded-lg border border-gray-11">
            <div class="grid lg:grid-cols-8 grid-cols-1">
                <div class="lg:col-span-2 border-r border-gray-11">
                    <div class="lg:mt-4 ltr:mr-4 rtl:ml-4 mt-8">
                        <img class="m-auto h-84p w-84p rounded-full" src="{{ optional($vendor->logo)->fileUrl() ?? $vendor->fileUrl() }}" alt="{{ __('Vendor Image') }}">
                        <div class="text-center mt-6 mb-6">
                            @if (in_array($shop->vendor_id, $topSellerIds))
                                <span
                                    class="primary-bg-color py-1.5 px-2 rounded-sm roboto-medium font-medium text-gray-12 text-xs">{{ __('Top Seller') }}</span>
                            @endif
                            <p class="text-gray-12 font-bold dm-bold text-2xl mt-2.5">{{ $vendor->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-6 w-full grid md:grid-cols-2 grid-cols-1 bg-gray-11">
                    <div>
                        <div class="bg-white flex justify-between items-center rounded-sm m-5 p-15p">
                            <div>
                                <p class="text-gray-12 dm-sans font-medium md:text-sm text-11">
                                    {{ __('Have any query') }}?</p>
                                <p class="text-gray-12 dm-bold font-bold md:text-lg text-15">
                                    {{ __('Send us a message') }}</p>
                            </div>
                            @if (isActive('Ticket') && preference('chat'))
                                <div>
                                    <a href="javascript:void(0)" class="primary-bg-color text-gray-12 font-bold dm-bold md:text-sm text-xs rounded-sm px-5 py-3.5 flex justify-center items-center" id="chat-initiate-vendor" data-vendor="{{ route('chat.initiate-chat-with-vendor', ['code' => base64_encode($vendor->id )]) }}">
                                        <span class="whitespace-nowrap">{{ __('Chat Now') }} </span>
                                        <svg class="ltr:ml-2 rtl:mr-2 w-3.5 md:w-18p h-3.5 md:h-18p"
                                            xmlns="http://www.w3.org/2000/svg" width="17" height="18" viewBox="0 0 17 18"
                                            fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.50461 3.79496e-07H9.49539C10.7786 -1.41442e-05 11.8134 -2.59653e-05 12.6436 0.0844338C13.5006 0.171614 14.2438 0.356532 14.9013 0.795839C15.4169 1.14036 15.8596 1.58308 16.2042 2.0987C16.6435 2.75617 16.8284 3.49938 16.9156 4.35638C17 5.18664 17 6.22141 17 7.50459V7.60651C17 8.8897 17 9.92447 16.9156 10.7547C16.8284 11.6117 16.6435 12.3549 16.2042 13.0124C15.8596 13.528 15.4169 13.9707 14.9013 14.3153C14.3271 14.6989 13.688 14.8884 12.9641 14.9885C12.3956 15.0671 11.7374 15.0948 10.9756 15.105L10.1895 16.6773C9.49337 18.0695 7.50663 18.0695 6.81053 16.6773L6.02435 15.105C5.26255 15.0948 4.6044 15.0671 4.03589 14.9885C3.31203 14.8884 2.67291 14.6989 2.0987 14.3153C1.58308 13.9707 1.14036 13.528 0.795839 13.0124C0.356532 12.3549 0.171614 11.6117 0.0844338 10.7547C-2.59653e-05 9.92447 -1.41442e-05 8.88969 3.79496e-07 7.6065V7.50461C-1.41442e-05 6.22142 -2.59653e-05 5.18664 0.0844338 4.35638C0.171614 3.49939 0.356532 2.75617 0.795839 2.0987C1.14037 1.58308 1.58308 1.14036 2.0987 0.795839C2.75617 0.356532 3.49939 0.171614 4.35639 0.0844338C5.18664 -2.59653e-05 6.22142 -1.41442e-05 7.50461 3.79496e-07ZM4.54755 1.96362C3.8399 2.03561 3.44348 2.16903 3.14811 2.36639C2.83874 2.57311 2.57311 2.83873 2.36639 3.14811C2.16903 3.44348 2.03561 3.8399 1.96362 4.54755C1.89003 5.27098 1.88889 6.20946 1.88889 7.55555C1.88889 8.90165 1.89003 9.84013 1.96362 10.5636C2.03561 11.2712 2.16903 11.6676 2.36639 11.963C2.57311 12.2724 2.83874 12.538 3.14811 12.7447C3.40628 12.9172 3.74211 13.041 4.29454 13.1174C4.86215 13.1958 5.59182 13.2165 6.61523 13.2209C6.99724 13.2226 7.32547 13.4509 7.47276 13.7781L8.5 15.8326L9.52724 13.7781C9.67453 13.4509 10.0028 13.2226 10.3848 13.2209C11.4082 13.2165 12.1379 13.1958 12.7055 13.1174C13.2579 13.041 13.5937 12.9172 13.8519 12.7447C14.1613 12.538 14.4269 12.2724 14.6336 11.963C14.831 11.6676 14.9644 11.2712 15.0364 10.5636C15.11 9.84013 15.1111 8.90165 15.1111 7.55555C15.1111 6.20946 15.11 5.27098 15.0364 4.54755C14.9644 3.8399 14.831 3.44348 14.6336 3.14811C14.4269 2.83873 14.1613 2.57311 13.8519 2.36639C13.5565 2.16903 13.1601 2.03561 12.4524 1.96362C11.729 1.89003 10.7905 1.88889 9.44444 1.88889H7.55556C6.20946 1.88889 5.27098 1.89003 4.54755 1.96362Z" fill="#2C2C2C" />
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.7207 5.6671C4.7207 5.1455 5.14355 4.72266 5.66515 4.72266L11.3318 4.72266C11.8534 4.72266 12.2763 5.1455 12.2763 5.6671C12.2763 6.1887 11.8534 6.61154 11.3318 6.61154L5.66515 6.61154C5.14355 6.61154 4.7207 6.1887 4.7207 5.6671Z" fill="#2C2C2C" />
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.7207 9.44444C4.7207 8.92284 5.14355 8.5 5.66515 8.5H8.49848C9.02008 8.5 9.44292 8.92284 9.44292 9.44444C9.44292 9.96605 9.02008 10.3889 8.49848 10.3889H5.66515C5.14355 10.3889 4.7207 9.96605 4.7207 9.44444Z" fill="#2C2C2C" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-gray-10 roboto-medium font-medium md:text-sm text-xs ltr:md:ml-5 rtl:md:mr-5 mx-5">{{ $vendor->description }}</p>
                        </div>
                    </div>
                    <div class="m-5">
                        <div class="grid grid-cols-2 gap-5">
                            <div class="hover:bg-gray-12 bg-white pt-5 ltr:pl-15p rtl:pr-15p hover:text-white text-gray-12 cursor-pointer dm-sans font-medium text-base rounded">
                                <p class="mb-3 w-28 break-all">{{ __('Positive Seller Ratings') }}
                                </p>
                                <p class="roboto-medium font-medium text-2.5xl mb-4">{{ round($positiveRating) }}%</p>
                            </div>
                            <div class="hover:bg-gray-12 bg-white hover:text-white cursor-pointer text-gray-12 pt-5 ltr:pl-15p rtl:pr-15p dm-sans font-medium text-base rounded">
                                <p class="mb-3 ltr:mr-9 rtl:ml-9">{{ __('Shipment on Time') }}
                                </p>
                                <p class="roboto-medium font-medium text-2.5xl text-green-3 mb-3">{{ $vendor->onTimeShipment() }}%</p>
                            </div>
                         <div class="pt-5 ltr:pl-15p rtl:pr-15p hover:bg-gray-12 bg-white cursor-pointer hover:text-white text-gray-12 dm-sans font-medium text-base rounded">
                                <p class="mb-3 ltr:mr-5 rtl:ml-5">{{ __('Seller’s Cancellation') }}</p>
                                <p class="roboto-medium font-medium text-2.5xl primary-text-color mb-4">{{ $vendor->orderCancel() }}%</p>
                            </div>
                            <div class="hover:bg-gray-12 bg-white hover:text-white cursor-pointer p-15p text-gray-12 dm-sans font-medium lg:text-base text-sm rounded">
                                <p class="w-32 ltr:pr-5 ltr:lg:pr-0 rtl:pl-5 rtl:lg:pl-0">{{ __('Seller Reviews') }}</p>
                                <p class="roboto-medium font-medium text-2.5xl text-green-3 pt-3 mb-3">{{ $reviewCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- review section --}}
        <div class="">
            <div class="review mt-11">
                <div class="mt-4">
                    <div class="grid grid-cols-12">
                        <div class="md:col-span-4 col-span-12">
                            <div class="flex items-center">
                                <p class="text-52 text-gray-12 dm-bold">{{ round($avg) }}</p>
                                <div class="ltr:pl-2.5 rtl:pr-2.5">
                                    <p class="roboto-medium text-base text-gray-12">{{ __('Seller Rating') }}</p>
                                    <ul class="flex items-center focus-within mt-1">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <li>
                                                <svg width="13" height="12" viewBox="0 0 16 15"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7.9048 0L9.72229 5.59367H15.6038L10.8456 9.05074L12.6631 14.6444L7.9048 11.1873L3.14654 14.6444L4.96404 9.05074L0.205779 5.59367H6.08731L7.9048 0Z" fill="{{ round($avg) >= $star ? 'var(--primary-color)' : '#C4C4C4' }}" />
                                                </svg>
                                            </li>
                                        @endfor
                                        <p class="text-gray-10 text-xs roboto-medium ltr:ml-1 rtl:mr-1">( {{ $reviewCount }}{{ $reviewCount > 1 ? __(' Reviews') : __(' Rating') }} )</p>
                                    </ul>
                                </div>
                            </div>
                            <div class="mb-1 tracking-wide py-4">
                                <div class="pb-3">
                                    @for ($progressBar = 5; $progressBar >= 1; $progressBar--)
                                        <div class="flex items-center mt-1">
                                            <div class="tracking-tighter ltr:mr-4 rtl:ml-4">
                                                <ul class="flex">
                                                    @for ($colorBar = 1; $colorBar <= 5; $colorBar++)
                                                        <li>
                                                            <svg class="ltr:mr-5p rtl:ml-5p" width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M7.9048 0L9.72229 5.59367H15.6038L10.8456 9.05074L12.6631 14.6444L7.9048 11.1873L3.14654 14.6444L4.96404 9.05074L0.205779 5.59367H6.08731L7.9048 0Z"
                                                                    fill="{{ $progressBar >= $colorBar ? 'var(--primary-color)' : '#C4C4C4' }}" />
                                                            </svg>
                                                        </li>
                                                    @endfor
                                                </ul>
                                            </div>
                                            @php
                                                $percent = 0;
                                                if (in_array($progressBar, array_column($progessBarRating, 'rating'))) {
                                                    $total_ratinges = array_column($progessBarRating, 'total_rating');
                                                    $percent = intval(($total_ratinges[array_search($progressBar, array_column($progessBarRating, 'rating'))] / $reviewCount) * 100);
                                                }
                                            @endphp
                                            <div class="w-full">
                                                <div class="bg-gray-6 w-full rounded-lg h-2">
                                                    <div data-width="{{ $percent }}" class="rating-width primary-bg-color color_switch_bac rounded-lg h-2">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-1/5 text-gray-700 ltr:pl-3 rtl:pr-3">
                                                <span class="text-sm">{{ $percent }}%</span>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-8 col-span-12 ltr:md:ml-12 ltr:text-left rtl:md:mr-12 rtl:text-right vendor-review-container">
                            <div id="review-section" class="flex justify-between items-center border-b pb-1">
                                <h2 class="font-bold text-gray-12 dm-bold text-base md:text-20">
                                    {{ __('Product Reviews') }}
                                </h2>
                                <div class="flex justify-center items-center">
                                    <div x-data="{ dropdownOpen: false }" class="relative ltr:ml-2 rtl:mr-2">
                                        <button @click="dropdownOpen = !dropdownOpen"
                                            class="inline-flex justify-between items-center gap-2 md:w-48 rounded md:px-2 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                            <div class="flex text-gray-500 items-center">
                                                <svg class="ltr:mr-5p rtl:ml-5p" width="14" height="14" viewBox="0 0 14 14"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 1.57238C0 0.703977 0.696446 0 1.55556 0H12.4444C13.3036 0 14 0.703977 14 1.57238V2.8191C14 3.23612 13.8361 3.63606 13.5444 3.93094L10.1111 7.40135V11.5095C10.1111 12.0171 9.78977 12.4677 9.31337 12.6283L5.42448 13.9386C4.66903 14.1931 3.88888 13.6247 3.88888 12.8198V7.40134L0.455612 3.93094C0.163888 3.63606 0 3.23612 0 2.8191V1.57238ZM12.4444 1.57238H1.55556V2.8191L4.98883 6.2895C5.28055 6.58438 5.44444 6.98432 5.44444 7.40134V12.2744L8.55555 11.2262V7.40135C8.55555 6.98433 8.71944 6.58439 9.01116 6.28951L12.4444 2.8191V1.57238Z" fill="#898989" />
                                                </svg>
                                                <div class="roboto-medium text-13 md:text-base text-gray-10">{{ __('Filter') }}:
                                                    <span class="filter-value" data-filter-star='0'>{{ __('All Star') }}</span>
                                                </div>
                                            </div>
                                            <svg class="w-2 h-1 md:w-0 md:h-0" width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.87867e-08 1.39309L1.5814 1.8858e-08L7.5 5.21383L13.4186 1.60015e-07L15 1.39309L7.5 8L7.87867e-08 1.39309Z" fill="#898989" />
                                            </svg>
                                        </button>
                                        <div x-show="dropdownOpen" @click="dropdownOpen = false"
                                            class="fixed inset-0 h-full w-full z-10"></div>
                                        <div x-show="dropdownOpen"
                                            class="absolute ltr:right-0 rtl:left-0 mt-2 py-2 w-24 md:w-48 bg-white rounded shadow z-20 roboto-medium"
                                            style="display: none;">
                                            <button @click="dropdownOpen = false" data-star="0"
                                                data-item="{{ $vendor->id }}"
                                                class="filter w-full ltr:text-left rtl:text-right px-3 py-2 text-sm capitalize hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                                <span class="primary-text-color text-md">✓</span><span
                                                    class="inline-block ltr:ml-3 rtl:mr-3 primary-text-color">All Star</span>
                                            </button>
                                            <button @click="dropdownOpen = false" data-star="5"
                                                data-item="{{ $vendor->id }}"
                                                class="filter w-full ltr:text-left rtl:text-right px-3 py-2 text-sm capitalize hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                                <span class="inline-block ltr:ml-6 rtl:mr-6">5 Star</span>
                                            </button>
                                            <button @click="dropdownOpen = false" data-star="4"
                                                data-item="{{ $vendor->id }}"
                                                class="filter w-full ltr:text-left rtl:text-right px-3 py-2 text-sm capitalize hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                                <span class="inline-block ltr:ml-6 rtl:mr-6">4 Star</span>
                                            </button>
                                            <button @click="dropdownOpen = false" data-star="3"
                                                data-item="{{ $vendor->id }}"
                                                class="filter w-full ltr:text-left rtl:text-right px-3 py-2 text-sm capitalize hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                                <span class="inline-block ltr:ml-6 rtl:mr-6">3 Star</span>
                                            </button>
                                            <button @click="dropdownOpen = false" data-star="2"
                                                data-item="{{ $vendor->id }}"
                                                class="filter w-full ltr:text-left rtl:text-right px-3 py-2 text-sm capitalize hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                                <span class="inline-block ltr:ml-6 rtl:mr-6">2 Star</span>
                                            </button>

                                            <button @click="dropdownOpen = false" data-star="1"
                                                data-item="{{ $vendor->id }}"
                                                class="filter w-full ltr:text-left rtl:text-right px-3 py-2 text-sm capitalize hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                                <span class="inline-block ltr:ml-6 rtl:mr-6">1 Star</span>
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="load_review" class="h-full">
                                @include('site.shop.review')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script src="{{ asset('public/dist/js/custom/site/wishlist.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/site/vendor-profile.min.js') }}"></script>
@endsection
