@extends('vendor.layouts.app')
@section('page_title', __('Receive Inventory'))

@section('content')

    <div class="col-sm-12" id="purchase-receive-container">

        {{-- Notification --}}
        <div class="col-md-12 no-print notification-msg-bar smoothly-hide">
            <div class="noti-alert pad">
                <div class="alert bg-dark text-light m-0 text-center">
                    <span class="notification-msg"></span>
                </div>
            </div>
        </div>

        <form action="{{ route('vendor.purchase.receiveStore', $purchase->id) }}" method="post">
            @csrf
            @include('inventory::common.purchase-receive')
        </form>
    </div>

@endsection
@section('js')
    <script src="{{ asset('Modules/Inventory/Resources/assets/js/receive.min.js') }}"></script>
@endsection
