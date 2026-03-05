@extends('vendor.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Invoice')]))
@section('css')
    <!-- date range picker css -->
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/css/invoice.min.css') }}">
@endsection
@section('content')

    <!-- Main content -->
    <div class="col-md-12" id="invoice-edit-container">
        {{-- Notification --}}
        <div class="col-md-12 no-print notification-msg-bar smoothly-hide">
            <div class="noti-alert pad">
                <div class="alert bg-dark text-light m-0 text-center">
                    <span class="notification-msg"></span>
                </div>
            </div>
        </div>
             
        @include('admin.orders.invoice.edit')
    </div>

    @include('admin.orders.modals.billing-address')
    @include('admin.orders.modals.shipping-address')
    @include('admin.orders.modals.add-fee')
    @include('admin.orders.modals.add-shipping-charge')
    @include('admin.orders.modals.add-coupon')
    @include('admin.orders.modals.add-discount')
@endsection
@section('js')
    <script>
        var orderView = "vendor";
        var GLOBAL_URL = "{{ route('site.index') }}";
        var hideDecimal = "{{ preference('hide_decimal', 0) }}";
        var taxes = @json($taxes);
        const orderListUrl = "{{ route('vendorOrder.index') }}";
        const billingAddress = @json($billingAddress->toArray());
        const shippingAddress = @json($shippingAddress->toArray());
        const oldShippingName = '{{ $order->shipping_name }}'
        const couponRedeem = @json($order->couponRedeems);
        const invoiceId = "{{ $order->id }}";
         
    </script>
    <script src="{{ asset('public/dist/js/custom/common.min.js') }}"></script>
    <!-- date range picker Js -->
    <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/order-invoice.min.js?v=4.2') }}"></script>
@endsection
