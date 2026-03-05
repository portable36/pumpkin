{{-- Discount Modal --}}
<div id="update_discount" class="modal fade display_none" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Add Discount') }}</h4>
                <a type="button" class="close h5" data-bs-dismiss="modal">×</a>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="coupon_code" class="form-label mb-2">{{ __('Discount based on') }}</label>
                            <div class="d-flex">
                                <button class="btn me-2 dollar-btn active discount-type" data-type="symbol" type="button">{{ currency()->symbol }}</button>
                                <button class="btn me-2 percent-btn discount-type" data-type="percent" type="button">%</button>
                                <input type="text" class="form-control positive-float-number discount-amount-input" placeholder="10" value="{{ isset($order) ? $order->other_discount_amount : 0 }}">
                            </div>
                            <span><small>{{ __('Percent discount will be applied on sub total') }}</small></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-0">
                <div class="form-group row">
                    <label for="btn_save" class="col-sm-3 control-label"></label>
                    <div class="col-sm-12 pe-0">
                        <button type="button"
                            class="py-2 custom-btn-cancel ltr:float-right rtl:float-left"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
