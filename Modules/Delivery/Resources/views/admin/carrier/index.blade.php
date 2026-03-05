@extends('admin.layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/Delivery/Resources/assets/css/delivery-module.min.css') }}">
@endsection
@section('page_title', __('All Delivery Boys'))

@section('content')
    <!-- Main content -->
    <div class="col-sm-12 list-container" id="carrier_list_container">
        <div class="card">
            <div class="card-header bb-none pb-0">
                <h5>{{ __('Delivery Boys') }}</h5>
                <x-backend.group-filters :groups="$groups" :column="'status'" />
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    <x-backend.button.batch-delete />
                    <x-backend.button.add-new href="{{ route('admin.delivery.carrier.create') }}" />
                    <x-backend.button.filter />
                </div>
            </div>

            <x-backend.datatable.filter-panel class="mx-1">
                <div class="col-md-6">
                    <x-backend.datatable.input-search />
                </div>
                <div class="col-md-3">
                    <select class="select2-hide-search filter" name="license_status">
                        <option value="">{{ __('All License') }}</option>
                        <option value="Pending">{{ __('Pending') }}</option>
                        <option value="Verified">{{ __('Verified') }}</option>
                        <option value="Invalid">{{ __('Invalid') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="select2-hide-search filter" name="is_active">
                        <option value="">{{ __('All Available') }}</option>
                        <option value="1">{{ __('Yes') }}</option>
                        <option value="0">{{ __('No') }}</option>
                    </select>
                </div>
            </x-backend.datatable.filter-panel>

            <x-backend.datatable.table-wrapper class="user-list-wallet user-list-processing-message">
                @include('admin.layouts.includes.yajra-data-table')
            </x-backend.datatable.table-wrapper>

            @include('admin.layouts.includes.delete-modal')
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
@endsection
