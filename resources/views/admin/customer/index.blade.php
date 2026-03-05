@extends('admin.layouts.app')
@section('page_title', __('Customers'))
@section('content')
    <!-- Main content -->
    <!-- Main content -->
    <div class="list-container" id="vendor-customer-list-container">
        <div class="card">
            <div class="card-header bb-none pb-0 mb-1">
                <h5>{{ __('Customers') }}</h5>
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    @hasPermission('App\Http\Controllers\CustomerController@create')
                        <x-backend.button.add-new href="{{ route('customers.create') }}" />
                    @endhasPermission
                    <x-backend.button.filter />
                </div>
            </div>

            <x-backend.datatable.filter-panel>
                <div class="col-md-6 px-3">
                    <x-backend.datatable.input-search />
                </div>
                <div class="col-md-6">
                    <select class="select2 filter vendor-ajax" name="vendor">
                        <option value="">{{ __('All Vendor') }}</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </x-backend.datatable.filter-panel>

            <x-backend.datatable.table-wrapper class="customer-list-table">
                @include('vendor.layouts.includes.yajra-data-table')
            </x-backend.datatable.table-wrapper>
            @include('admin.layouts.includes.delete-modal')
        </div>
    </div>
@endsection
