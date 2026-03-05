@extends('delivery::layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/Delivery/Resources/assets/css/delivery-module.min.css') }}">
@endsection
@section('page_title', __('Earnings history'))

@section('content')
    <!-- Main content -->
    <div class="col-sm-12 list-container" id="carrier_order_list_container">
        <div class="card">
            <div class="card-body p-0 carrier-list" data-column="id">
                <div class="card-block pt-2 px-2">
                    <div class="col-sm-12 form-tabs px-3">
                        @include('admin.layouts.includes.yajra-data-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    "use strict";
    var pdf = 1;
    var csv = 1;
</script>
<script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
<script>
    exportPdfCsv('#subscriber-list-container', '/delivery/carrier/all/');
</script>
@endsection
