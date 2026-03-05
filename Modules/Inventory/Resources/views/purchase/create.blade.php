@extends('admin.layouts.app')
@section('page_title', __('Create :x', ['x' => __('Purchase')]))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/datta-able/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/jQueryUI/jquery-ui.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/Inventory/Resources/assets/css/purchase.min.css') }}">
@endsection

@section('content')

    <div class="col-sm-12" id="purchase-add-container">

        {{-- Notification --}}
        <div class="col-md-12 no-print notification-msg-bar smoothly-hide">
            <div class="noti-alert pad">
                <div class="alert bg-dark text-light m-0 text-center">
                    <span class="notification-msg"></span>
                </div>
            </div>
        </div>
        
        <form action="{{ route('purchase.store') }}" method="post" id="purchase_form">
            @csrf
            
            @include('inventory::common.purchase-create', ['userType' => 'admin'])
            @include('inventory::layouts.purchase_settings')
        </form>
    </div>

    @include('inventory::layouts.add_supplier')
    @include('inventory::layouts.add_location')
    
@endsection
@section('js')
    <script src="{{ asset('/public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/datta-able/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"></script>
    <script>
        var rowNo = 0;
        var totalAmount = 0;
        var addSupplierUrl = '{{ route('supplier.store') }}';
        var addLocation = '{{ route('location.store') }}'
        
        let oldCountry = "{!! old('country') ?? 'null' !!}";
        let oldState = "{!! old('state') ?? 'null' !!}";
        let oldCity = "{!! old('city') ?? 'null' !!}";
        let url = "{{ URL::to('/') }}";
        var thousandSeparator = '{{ preference('thousand_separator') }}';
        var vendorUrl = '{{ route('find.vendors.ajax') }}';
        var shippingProviderUrl = '{{ route('search.shipping.providers') }}';
    </script>
     <script src="{{ asset('Modules/Inventory/Resources/assets/js/purchase.min.js?v=5.0') }}"></script>
    <script src="{{ asset('Modules/Inventory/Resources/assets/js/location.min.js') }}"></script>
@endsection
