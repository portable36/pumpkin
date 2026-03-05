{{-- Shipping Modal --}}
<div id="update_shipping" class="modal fade display_none" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Shipping Fee') }}</h4>
                <a type="button" class="close h5" data-bs-dismiss="modal">×</a>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row radio shipping-fee-row">
                            <p class="col-12 text-center">{{ __('The shipping fee has not loaded yet.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-0">
                <div class="form-group row">
                    <label for="btn_save" class="col-sm-3 control-label"></label>
                    <div class="col-sm-12">
                        <button type="button"
                            class="py-2 custom-btn-cancel ltr:float-right rtl:float-left"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
