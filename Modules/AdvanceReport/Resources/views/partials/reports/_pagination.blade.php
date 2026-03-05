{{-- Pagination Component --}}
{{-- Usage: @include('advancereport::partials.reports._pagination', ['paginator' => $data['paginator']]) --}}
@if(isset($paginator) && $paginator->total() > 0)
    <div class="report-pagination-wrapper">
        <div class="report-pagination-content">
            <div class="report-pagination-info">
                {{ __('Showing :x to :y of :z entries', [
                    'x' => $paginator->firstItem() ?? 0,
                    'y' => $paginator->lastItem() ?? 0,
                    'z' => $paginator->total()
                ]) }}
            </div>
            @if($paginator->hasPages())
            <div class="report-pagination-controls">
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    
                    // Show maximum 5 pages around current page
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp
                
                @if(!$paginator->onFirstPage())
                    <button type="button" class="report-pagination-btn report-pagination-btn-prev report-pagination-link" data-page="{{ $currentPage - 1 }}">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                @else
                    <button type="button" class="report-pagination-btn report-pagination-btn-prev" disabled>
                        <i class="fa fa-chevron-left"></i>
                    </button>
                @endif
                
                <div class="report-pagination-pages">
                    @if($startPage > 1)
                        <button type="button" class="report-pagination-page report-pagination-link" data-page="1">1</button>
                        @if($startPage > 2)
                            <span class="report-pagination-dots">...</span>
                        @endif
                    @endif
                    
                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <button type="button" class="report-pagination-page report-pagination-page-active" disabled>{{ $page }}</button>
                        @else
                            <button type="button" class="report-pagination-page report-pagination-link" data-page="{{ $page }}">{{ $page }}</button>
                        @endif
                    @endfor
                    
                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span class="report-pagination-dots">...</span>
                        @endif
                        <button type="button" class="report-pagination-page report-pagination-link" data-page="{{ $lastPage }}">{{ $lastPage }}</button>
                    @endif
                </div>
                
                @if($paginator->hasMorePages())
                    <button type="button" class="report-pagination-btn report-pagination-btn-next report-pagination-link" data-page="{{ $currentPage + 1 }}">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                @else
                    <button type="button" class="report-pagination-btn report-pagination-btn-next" disabled>
                        <i class="fa fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            @endif
        </div>
    </div>
@endif
