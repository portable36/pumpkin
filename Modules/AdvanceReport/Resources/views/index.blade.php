@extends('admin.layouts.app')
@section('page_title', __('Advance Reports'))
@section('css')
<link rel="stylesheet" href="{{ asset('Modules/AdvanceReport/Resources/assets/css/style.min.css') }}">
@endsection
@section('content')
@use('Modules\AdvanceReport\Services\ReportConfigService')
@php
    $allReports = ReportConfigService::getAllReports();
@endphp
<div class="col-sm-12 list-container advance-report-container">
    <!-- Header Section -->
    <div class="report-header-section">
        <div class="report-header-content">
            <div class="report-header-left">
                <div class="report-header-icon">
                    <i class="fa fa-chart-bar"></i>
                </div>
                <div class="report-header-title">
                    <h4 class="report-main-title">{{ __('Advance Reports') }}</h4>
                    <p class="report-subtitle">{{ __('Analyze your business performance with detailed insights') }}</p>
                </div>
            </div>
            <div class="report-header-right">
                <form action="{{ route('advance-reports') }}" method="get" class="report-search-form">
                    <div class="report-search-wrapper">
                        <i class="fa fa-search report-search-icon"></i>
                        <input type="text" name="search" class="report-search-input" placeholder="{{ __('Search reports') }}" value="{{ $search }}">
                        @if(!empty($categories))
                            @foreach($categories as $cat)
                            <input type="hidden" name="categories[]" value="{{ $cat }}">
                            @endforeach
                        @endif
                    </div>
                    <button type="submit" class="report-search-btn">
                        {{ __('Search') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Filters and Category Section (Combined) -->
    <div class="report-category-filter-section">
        <div class="report-category-filter-content">
            <!-- Category Filter -->
            <div class="report-category-select-wrapper">
                <label class="report-category-label">
                    <i class="fa fa-filter mr-2"></i>{{ __('Category') }}
                </label>
                <div class="custom-category-dropdown" id="custom-category-dropdown">
                    <input type="hidden" name="categories[]" id="category-filter-value" value="">
                    <div class="custom-dropdown-trigger">
                        <span class="custom-dropdown-selected">
                            @php
                                $categories = array_unique($categories);
                                $selectedCategories = $categories ?? [];
                            @endphp
                        
                            @if (empty($selectedCategories))
                                {{ __('All Categories') }}
                            @elseif (count($selectedCategories) === 1)
                                @foreach ($allReports as $catKey => $categoryData)
                                    @if ($categoryData['slug'] === $selectedCategories[0])
                                        {{ $categoryData['category'] }}
                                        @break
                                    @endif
                                @endforeach
                            @else
                                {{ count($selectedCategories) . ' ' . __('categories selected') }}
                            @endif
                        </span>
                        <i class="fa fa-chevron-down custom-dropdown-arrow"></i>
                    </div>
                    <div class="custom-dropdown-menu">
                        <div class="custom-dropdown-search">
                            <i class="fa fa-search"></i>
                            <input type="text" placeholder="{{ __('Search category') }}" class="custom-dropdown-search-input">
                        </div>
                        <div class="custom-dropdown-options">
                            @php
                                $selectedCategories = $categories ?? [];
                            @endphp
                            @foreach($allReports as $catKey => $categoryData)
                            @if($categoryData['enabled'])
                            <div class="custom-dropdown-option" data-value="{{ $categoryData['slug'] }}">
                                <label class="custom-checkbox-label">
                                    <input type="checkbox" class="category-checkbox" value="{{ $categoryData['slug'] }}" {{ in_array($categoryData['slug'], $selectedCategories) ? 'checked' : '' }}>
                                    <span class="option-text">{{ $categoryData['category'] }}</span>
                                </label>
                                @if(in_array($categoryData['slug'], $selectedCategories))
                                <i class="fa fa-check option-check"></i>
                                @endif
                            </div>
                            @endif
                            @endforeach
                        </div>
                        <div class="custom-dropdown-footer">
                            <button type="button" class="btn-apply-categories">{{ __('Apply') }}</button>
                            <button type="button" class="btn-clear-categories">{{ __('Clear All') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            <div class="report-active-filters">
                @if($search || !empty($categories))
                    <span class="report-filters-label">{{ __('Active filters') }}:</span>
                    @if(!empty($categories))
                        @foreach($categories as $catSlug)
                            @foreach($allReports as $catKey => $categoryData)
                                @if($categoryData['slug'] === $catSlug)
                                <span class="report-filter-badge">
                                    <span class="badge-text">{{ __('Category') }}: {{ $categoryData['category'] }}</span>
                                    <a href="{{ route('advance-reports', array_merge(['search' => $search], ['categories' => array_values(array_diff($categories, [$catSlug]))])) }}" class="badge-close" title="{{ __('Remove filter') }}">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </span>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                    @if($search)
                    <span class="report-filter-badge">
                        <span class="badge-text">{{ __('Search') }}: {{ $search }}</span>
                        <a href="{{ route('advance-reports', ['categories' => $categories ?? []]) }}" class="badge-close" title="{{ __('Remove filter') }}">
                            <i class="fa fa-times"></i>
                        </a>
                    </span>
                    @endif
                    @if($search || !empty($categories))
                    <a href="{{ route('advance-reports') }}" class="report-clear-all">
                        <i class="fa fa-times-circle me-1 mt-1"></i>{{ __('Clear all') }}
                    </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="report-grid-section">
        <div class="report-grid">
            @forelse($reports as $catKey => $categoryData)
                @foreach($categoryData['reports'] as $report)
                <a href="{{ route('advance-reports.show', $report['slug']) }}" class="report-card">
                    <div class="report-card-header">
                        <div class="report-icon">
                            @php
                                $icon = 'icon-bar-chart-2';
                                if (stripos($categoryData['slug'], 'customers') !== false) {
                                    $icon = 'icon-user';
                                } elseif (stripos($categoryData['slug'], 'pos') !== false) {
                                    $icon = 'icon-monitor';
                                } elseif (stripos($categoryData['slug'], 'sales') !== false) {
                                    $icon = 'icon-box';
                                } elseif (stripos($categoryData['slug'], 'finances') !== false || stripos($categoryData['slug'], 'payments') !== false) {
                                    $icon = 'icon-briefcase';
                                }
                            @endphp
                            <i class="feather {{ $icon }}"></i>
                        </div>
                        <span class="report-category-badge category-{{ $categoryData['slug'] }}">
                            {{ $categoryData['category'] }}
                        </span>
                    </div>
                    <div class="report-card-body">
                        <h6 class="report-title">{{ $report['name'] }}</h6>
                        <p class="report-description">{{ $report['description'] }}</p>
                    </div>
                    <div class="report-card-footer">
                        <span class="report-link-text">{{ __('View report') }} <i class="fa fa-arrow-right ml-1"></i></span>
                    </div>
                </a>
                @endforeach
            @empty
                <div class="no-reports-found">
                    <i class="fa fa-inbox mb-3" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mb-0">{{ __('No reports found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
    @section('js')
    <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/common.min.js') }}"></script>
    <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report-config.min.js') }}"></script>
    <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report-show.min.js') }}"></script>
    <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/table.min.js') }}"></script>
    <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report-list.min.js') }}"></script>
    <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report.min.js') }}"></script>
    @endsection
