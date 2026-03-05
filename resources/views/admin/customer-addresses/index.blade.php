@extends('admin.layouts.app')
@section('page_title', __('Customer Addresses'))
@section('content')
    <!-- Main content -->
    <div class="list-container" id="admin-customer-list-container">
        <div class="card">
            <div class="card-header bb-none pb-0 mb-1">
                <h5>{{ __(':x Billing & Shipping Address', ['x' => $customer->name ]) }}</h5>
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    @hasPermission('App\Http\Controllers\CustomerAddressController@create')
                        <x-backend.button.add-new href="{{ route('customer.addresses.create', ['customer' => $customer->id]) }}" />
                    @endhasPermission
                    <x-backend.button.filter />
                </div>
            </div>

            <x-backend.datatable.filter-panel>
                <div class="col-md-12 px-3">
                    <x-backend.datatable.input-search />
                </div>
                
            </x-backend.datatable.filter-panel>

            @include('common.customer.top-menu', ['customer' => $customer, 'from' => 'admin', 'menuName' => 'addresses'])

            <x-backend.datatable.table-wrapper class="customer-address-list-table">
                @include('vendor.layouts.includes.yajra-data-table')
            </x-backend.datatable.table-wrapper>
            @include('admin.layouts.includes.delete-modal')
        </div>
    </div>
@endsection
