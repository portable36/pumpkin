@extends('vendor.layouts.app')
@section('page_title', __('Customers'))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/dist/css/vendor-responsiveness.min.css') }}">
@endsection
@section('content')

    <!-- Main content -->
    <div class="list-container" id="customer-ledger-container">
        <div class="card">
            <div class="card-header pb-4 mb-1">
                <h5>{{ __(':x Billing & Shipping Address', ['x' => $customer->name ]) }}</h5>
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    @hasPermission('App\Http\Controllers\Vendor\CustomerAddressController@create')
                        <x-backend.button.add-new href="{{ route('vendor.customer.addresses.create', ['customer' => $customer->id]) }}" />
                    @endhasPermission
                    <x-backend.button.filter />
                </div>
            </div>

            <x-backend.datatable.filter-panel class="mx-1">
                <div class="col-md-12 px-3">
                    <x-backend.datatable.input-search />   
                </div>
            </x-backend.datatable.filter-panel>

            @include('common.customer.top-menu', ['customer' => $customer, 'from' => 'vendor', 'menuName' => 'addresses'])

            <x-backend.datatable.table-wrapper class="order-list-table">
                @include('vendor.layouts.includes.yajra-data-table')
            </x-backend.datatable.table-wrapper>
            @include('admin.layouts.includes.delete-modal')
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/yajra-export.min.js') }}"></script>
@endsection
