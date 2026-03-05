@extends('vendor.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Purchase')]))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/datta-able/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/jQueryUI/jquery-ui.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/Inventory/Resources/assets/css/purchase.min.css') }}">
@endsection

@section('content')

    <div class="col-sm-12" id="purchase-edit-container">

        {{-- Notification --}}
        <div class="col-md-12 no-print notification-msg-bar smoothly-hide">
            <div class="noti-alert pad">
                <div class="alert bg-dark text-light m-0 text-center">
                    <span class="notification-msg"></span>
                </div>
            </div>
        </div>
        
        <form action="{{ route('vendor.purchase.update', $purchaseDetails->id) }}" method="post" id="purchase_form">
            @csrf
            <input type="hidden" name="vendor_id" value="{{ $purchaseDetails->vendor_id }}">

            @include('inventory::common.purchase-edit')
            @include('inventory::layouts.purchase_settings')
        </form>
    </div>
@endsection
@section('js')
    <script src="{{ asset('/public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/datta-able/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"></script>
    <script>
        var totalAmount = '{{ $purchaseDetails->total_amount }}';
        var thousandSeparator = '{{ preference('thousand_separator') }}';
        var shippingProviderUrl = '{{ route('search.shipping.providers') }}';
    </script>
    <script src="{{ asset('Modules/Inventory/Resources/assets/js/purchase.min.js?v=4.2') }}"></script>
@endsection
