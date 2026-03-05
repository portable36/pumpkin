{{-- Fee Modal --}}
<div id="update_fee" class="modal fade display_none" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Adjust Fee') }}</h4>
                <a type="button" class="close h5" data-bs-dismiss="modal">×</a>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 p-2">
                        <table class="table" id="adjust_table">
                            <thead>
                                <tr>
                                    <th class="text-center w-50">{{ __('Fee Type') }}</th>
                                    <th class="text-center w-25">{{ __('Amount') }}</th>
                                    <th class="text-center w-5">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="adjustRowId" class="adjustFields">
                                @forelse (json_decode(isset($order) ? $order->fee : '', true) ?? [] as $fee)
                                    <tr class="adjustRow">
                                        <td class="text-center ps-0">
                                            <input type="text" name="adjustment[name][]" class="inputAdjustName inputFieldDesign form-control text-center" value="{{ $fee['name'] ?? '' }}">
                                        </td>
                                        <td class="text-center">
                                            <input name="adjustment[amount][]" class="inputAdjustAmount inputFieldDesign form-control text-center positive-float-number" type="text" value="{{ $fee['amount'] ?? 0 }}">
                                        </td>
                                        @if ($loop->first)
                                            <td class="text-center padding_top_18px"></td>
                                        @else
                                            <td class="text-center padding_top_18px">
                                                <a class="delete-adjustment" href="javascript:void(0)"><i class="feather icon-trash f-16 d-inline-block mt-1 pt-2"></i></a>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr class="adjustRow">
                                        <td class="text-center ps-0">
                                            <input type="text" name="adjustment[name][]" class="inputAdjustName inputFieldDesign form-control text-center">
                                        </td>
                                        <td class="text-center">
                                            <input name="adjustment[amount][]" class="inputAdjustAmount inputFieldDesign form-control text-center positive-float-number" type="text" value="0">
                                        </td>
                                        <td class="text-center padding_top_18px"></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <a class="options-add-two mt-2" id="adjustmentBtn">{{ __('Add More Fee') }}</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-0">
                <div class="form-group row">
                    <label for="btn_save" class="col-sm-3 control-label"></label>
                    <div class="col-sm-12">
                        <button type="button"
                            class="py-2 custom-btn-cancel ltr:float-right"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
