@extends('vendor.layouts.app')
@section('page_title', __('View :x', ['x' => __('Purchase')]))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/dist/css/invoice.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}">
@endsection
@section('content')

    <!-- Main content -->
    <div class="col-md-12" id="invoice-view-container">
        {{-- Notification --}}
        <div class="col-md-12 no-print notification-msg-bar smoothly-hide">
            <div class="noti-alert pad">
                <div class="alert bg-dark text-light m-0 text-center">
                    <span class="notification-msg"></span>
                </div>
            </div>
        </div>
             
        @include('inventory::common.purchase-view', ['panel' => 'vendor'])
    </div>
@endsection

@section('js')
    <script src="{{ asset('public/dist/js/custom/common.min.js') }}"></script>
    <!-- date range picker Js -->
    <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/Inventory/Resources/assets/js/purchase-view.min.js') }}"></script>
@endsection
