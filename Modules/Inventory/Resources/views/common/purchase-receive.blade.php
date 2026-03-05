<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>{{ __('Receive Inventory') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 m-t-10 p-2">
                    <table class="table" id="product-table">
                        <thead>
                        <tr class="tbl_header_color">
                            <th class="text-center">{{ __('Products') }}</th>
                            <th class="text-center">{{ __('Unit') }}</th>
                            <th class="text-center">{{ __('Supplier SKU') }}</th>
                            <th class="text-center">
                                {{ __('Receive') }}
                            </th>
                            <th class="text-center">
                                {{ __('Reject') }}
                            </th>
                            <th class="text-center">
                                {{ __('Availability') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                    
                        @foreach($purchase->purchaseDetail as $data)
                            @php 
                            $isEditable = false;
                            
                            $recRej = $data->quantity_receive + $data->quantity_reject;
                            
                            if ($recRej <  $data->quantity) {
                                $isEditable = true;
                            }
                            
                            @endphp
                        <tr>
                            <td class="text-center">{!! wrapIt($data->product_name, 10, ['columns' => 2]) !!}</td>
                            <td class="text-center">{{ $data->unit ?? defaultUnit()?->abbr }}</td>
                            <td class="text-center">{{ $data->sku }}</td>
                            <td class="text-center"><input id="product_receive_{{ $data->id }}" data-qty = '{{ $data->quantity }}' data-rec = '{{ $data->quantity_receive ?? 0 }}' data-rej = '{{ $data->quantity_reject ?? 0 }}' data-rowId="{{ $data->id }}" name="products[receive][{{ $data->id }}]" class="inputReceive form-control text-center positive-float-number" type="text" placeholder="0.00"  value="0" {{ !$isEditable ? 'readonly' : '' }}></td>
                            <td class="text-center"><input id="product_reject_{{ $data->id }}" data-qty = '{{ $data->quantity }}' data-rec = '{{ $data->quantity_receive ?? 0 }}' data-rej = '{{ $data->quantity_reject ?? 0 }}' name="products[reject][{{ $data->id }}]" class="inputReject form-control text-center positive-float-number" type="text" placeholder="0.00"  value="0" {{ !$isEditable ? 'readonly' : '' }}></td>
                            <td class="text-center">
                                <strong>{{ formatCurrencyAmount($data->quantity) }}</strong>
                                {{ __('Units') }}
                                —
                                {{ __('Received') }}:
                                <strong>{{ formatCurrencyAmount($data->quantity_receive ?? 0) }}</strong>,
                                {{ __('Rejected') }}:
                                <strong>{{ formatCurrencyAmount($data->quantity_reject ?? 0) }}</strong>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div id="error_message" class="text-danger col-md-8 p-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-10 px-0 mt-3 mt-md-0">
        <a href="{{ route('purchase.index') }}"
            class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
        <button class="btn custom-btn-submit" type="submit" id="btnSubmit"><i
                class="comment_spinner spinner fa fa-spinner fa-spin custom-btn-small display_none"></i><span
                id="spinnerText">{{ __('Update') }}</span></button>
    </div>
</div>
