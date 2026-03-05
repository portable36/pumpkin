{{-- Payment Modal --}}

<div id="purchase_payment" class="modal fade display_none" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route($paymentRoute, $purchase->id) }}" method="post" id="payment_form">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Payment for purchase :x', ['x' => '#' . $purchase->reference]) }}</h4>
                    <a type="button" class="close h5" data-bs-dismiss="modal">×</a>
                </div>

                <input type="hidden" name="vendor_id" value="{{ $purchase->vendor_id }}">
                <input type="hidden" name="supplier_id" value="{{ $purchase->supplier_id }}">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label require">{{ __('Amount Paid') }}:</label>
                            <input class="form-control inputFieldDesign" name="amount_paid" required step="0.00000001" min="0" max="{{ $purchase->total_amount - $purchase->paid_amount }}" id="amount_paid" type="number" value="{{ $purchase->total_amount - $purchase->paid_amount }}">
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="control-label require">{{ __('Payment Date') }}:</label>
                            <input class="form-control inputFieldDesign" required name="payment_date" id="payment_date" value='{{ date('Y-m-d') }}' type="text">
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="control-label">{{ __('Payment Method') }}:</label>
                            <select class="form-control select2 sl_common_bx" name="payment_method" id="payment_method">
                                <option value="">{{ __('Select One') }}</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->name }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mt-2">
                            <h6>{{ __('Note') }}:</h6>
                            <textarea class="form-control inputFieldDesign" name="description" id="description" rows="3" placeholder="{{ __('Write your note...') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-0">
                    <div class="form-group row">
                        <label for="btn_save" class="col-sm-3 control-label"></label>
                        <div class="col-sm-12 pe-0">
                            <button type="submit"
                                class="py-2 btn custom-btn-submit ltr:float-right rtl:float-left me-0 apply-payment-btn">{{ __('Apply') }}</button>
                            <button type="button"
                                class="py-2 custom-btn-cancel ltr:float-right ltr:me-2 rtl:float-left rtl:ms-2"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
