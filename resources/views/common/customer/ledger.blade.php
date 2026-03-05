@extends($from . '.layouts.app')
@section('page_title', __('Customer Ledger'))
@section('content')
    <!-- Main content -->
    <div class="list-container" id="customer-list-container">
        <div class="card">
            <div class="card-header bb-none pb-0 mb-1">
                <h5>{{ __(':x Ledger', ['x' => $customer?->name ]) }}</h5>
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    <x-backend.button.filter />
                    @if ($orderTotal - $transactionTotal > 0)
                        <a class="btn btn-square btn-primary f-w-600 btn-sm mb-0 collapsed filterbtn ltr:me-1 rtl:ms-1" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#customer_payment">
                            {{ __('Payment') }}
                        </a>
                    @endif
                    @include('common.modals.customer-payment')
                </div>
            </div>

            <x-backend.datatable.filter-panel>
                <div class="col-md-12 px-3">
                    <x-backend.datatable.input-search />
                </div>
            </x-backend.datatable.filter-panel>

            @include('common.customer.top-menu', ['customer' => $customer, 'from' => $from, 'menuName' => 'ledger'])

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
@section('js')
    <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/customer-ledger.min.js') }}"></script>
@endsection
