@extends('admin.layouts.app')
@section('page_title', __('Customer Ledger'))
@section('content')
    <!-- Main content -->
    <div class="list-container" id="customer-ledger-container">
        <div class="card">
            <div class="card-header pb-4 mb-1">
                <h5>{{ __(':x Ledger', ['x' => $user?->name ]) }}</h5>
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    <x-backend.button.filter />
                </div>
            </div>

            <x-backend.datatable.filter-panel class="mx-1">
                <div class="col-md-12 px-3">
                    <x-backend.datatable.input-search />
                </div>
            </x-backend.datatable.filter-panel>

            @include('admin.layouts.includes.user_menu' )

            <div class="mx-4 mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">{{ __('Total Amount') }}</h5>
                                <h3 class="text-center">{{ formatNumber($orderTotal) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">{{ __('Total Paid Amount') }}</h5>
                                <h3 class="text-center">{{ formatNumber($transactionTotal) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">{{ __('Total Due') }}</h5>
                                <h3 class="text-center">{{ formatNumber($orderTotal - $transactionTotal) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-backend.datatable.table-wrapper class="order-list-table">
                @include('vendor.layouts.includes.yajra-data-table')
            </x-backend.datatable.table-wrapper>
        </div>
    </div>
@endsection
