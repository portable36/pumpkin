<div class="card">
    <div class="card-header">
        <h5>{{ __('Delivery') }}</h5>
        <div class="card-header-right">
            <div class="btn-group card-option card-accordion">
                <button type="button" class="btn dropdown-toggle drop-down-icon text-mute">
                    <i class="fas fa-angle-down"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="sections-body accordion-body">
        <div class="order-sections-body cursor_pointer">
            <div class="border-top">
                <button class="w-100" {{ in_array($order->order_status_id, getOrderStatusIds(['delivered', 'completed', 'pickup'])) ? 'disabled' : '' }} id="assign-carrier-btn" data-bs-toggle="modal" data-bs-target="#assign-carrier-modal">{{ __('Assign Delivery Boy') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Modal -->
<div class="modal fade" id="assign-carrier-modal" tabindex="-1" aria-labelledby="assign-carrier-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Assign Delivery Boy') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="assign-carrier-modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary custom-btn-small" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary custom-btn-small" id="assign-carrier-modal-refresh-btn">{{ __('Refresh') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- End Delivery Modal -->

<script>
    let ROOT_URL = "{{ URL::to('/') }}";
    var MAIN_URL = "{{ url('/') }}" + '/admin';
</script>

<script src="{{ asset('Modules/Delivery/Resources/assets/js/delivery-order.min.js') }}"></script>
