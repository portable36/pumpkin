@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Transfer')]))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/datta-able/plugins/select2/css/select2.min.css') }}">

    <link rel="stylesheet" href="{{ asset('public/dist/plugins/jQueryUI/jquery-ui.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/Inventory/Resources/assets/css/purchase.min.css') }}">
@endsection

@section('content')

    <div class="col-sm-12" id="transfer-edit-container">

        <form action="{{ route('transfer.update', $transferDetails->id) }}" method="post">
            @csrf
            @php
                $isEditProduct = $transferDetails->status == 'Pending';
       
            @endphp
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{  __('Edit :x', ['x' => __('Transfer')]) }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">


                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-form-label col-md-4">{{ __('Vendor') }}
                                    </label>
                                    <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $transferDetails->vendor_id }}">
                                    <div class="col-md-8">
                                        <select class="form-control" readonly="true">
                                            <option value="{{ $transferDetails->vendor_id }}" selected>{{ $transferDetails->vendor->name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-form-label col-md-4">{{ __('Reference') }}</label>
                                    <div class="col-md-8">
                                        <input class="form-control inputFieldDesign"  value="{{ $transferDetails->reference }}" type="text" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label require">{{ __('Origin') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control" readonly = "true" name="from_location_id" id="from_location_id" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" {!! $isEditProduct ? '' : 'readonly = true' !!}>
                                            <option value="{{ $transferDetails->from_location_id }}" selected>{{ $transferDetails->fromLocation->name  }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label require">{{ __('Destination') }}</label>
                                    <div class="col-md-8">
                                        <select class="form-control" readonly = "true" name="to_location_id" id="to_location_id" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" {!! $isEditProduct ? '' : 'readonly = true' !!}>
                                            <option value="{{ $transferDetails->to_location_id }}" selected>{{ $transferDetails->toLocation->name  }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Products') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($isEditProduct)
                        <div class="row">
                            <label class="col-form-label col-md-4" for="add_products">{{ __('Add Products') }}
                            </label>
                            <div class="col-md-8">
                                <input id="search" class="form-control inputFieldDesign" type="text" name="search" placeholder="{{ __('Search for product by name') }}">
                                <li id="no_div">
                                    <div>
                                        <label>{{__('No data found')}}</label>
                                    </div>
                                </li>
                            </div>
               
                                <div class="col-md-4"></div>
                                <div id="error_message" class="text-danger col-md-4"></div>
                  
                        </div>

                        <br>
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table" id="product-table">
                                    <thead>
                                    <tr>
                                        <th>{{ __('Products') }}</th>
                                        <th class="text-center">{{ __('SKU') }}</th>
                                        <th class="itemQty text-center">
                                            {{ __('Quantity') }}
                                        </th>
                                        @if($isEditProduct)
                                        <th class="w-5">{{ __('Action') }}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    @php
                                        $rowNo = 0;
                                    @endphp
                                    @foreach($transferDetails->transferDetail as $key => $detail)
                                        @php 
                                            $rowNo = $key; 
                                            $stack[] = $detail->product_id
                                        @endphp

                                        <tbody id="rowId-{{ $key }}" class="transfer_products">
                                        <input type="hidden" name="product_id[]" value="{{ $detail->product_id }}">
                                        <tr class="itemRow rowNo-{{ $key }}" id="productId-{{ $detail->product_id }}"  data-row-no="{{ $key }}">
                                            <td class="pl-1">
                                                {{ $detail->product?->name }}
                                            </td>

                                            <td class="sup_sku">
                                                <input id="sku_{{ $key }}" class="form-control text-center" type="text" readonly value="{{ $detail->product?->sku }}">
                                            </td>

                                            <td class="productQty">
                                                <input name="product_qty[]" id="product_qty_{{ $key }}" class="inputQty form-control text-center" type="number" value="{{ formatDecimal($detail->quantity) }}" min="0" max="{{ $detail->product?->total_stocks }}" data-rowId = {{ $key }} {{ $isEditProduct ? '' : 'readonly'}}>
                                            </td>
                                            @if($isEditProduct)
                                            <td class="text-center padding_top_15px">
                                                <a href="javascript:void(0)" class="closeRow" data-row-id="{{ $key }}" data-id="{{ $detail->product_id }}"><i class="feather icon-trash"></i></a>
                                            </td>
                                            @endif
                                        </tr>

                                        </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Shipment Details') }}</h5>
                            </div>
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">{{ __('Estimate Arrival Date') }}</label>
                                    <div class="col-md-8">
                                        <input id="arrival_date" class="form-control inputFieldDesign" type="text" name="arrival_date" value="{{ $transferDetails->arrival_date }}">
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-md-4">{{ __('Shipping Carrier') }}
                                    </label>
                                    <div class="col-md-8">
                                        <select class="form-control select2 shipping-provider-ajax" name="shipping_carrier" id="shipping_carrier">
                                            <option value="{{ $transferDetails?->shipping_carrier }}">{{ $shippingProviders->where('slug', $transferDetails?->shipping_carrier)->first()?->name }}</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-md-4">{{ __('Tracking number') }}
                                    </label>
                                    <div class="col-md-8">
                                        <input id="tracking_number" class="form-control inputFieldDesign" type="text" name="tracking_number" placeholder="{{ __('Tracking number') }}" value="{{ $transferDetails->tracking_number }}">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Additional Details') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-form-label col-md-4">{{ __('Note') }}
                                    </label>
                                    <div class="col-md-8">
                                        <textarea placeholder="{{ __('Note') }}" rows="3" class="form-control" name="note" spellcheck="false">{{ $transferDetails->note }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-10 px-0 mt-3 mt-md-0">
                    <a href="{{ route('transfer.index') }}"
                       class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
                    <button class="btn custom-btn-submit" type="submit" id="btnSubmit" {{ $transferDetails->status == 'Received' ? 'disabled' : '' }}><i
                            class="comment_spinner spinner fa fa-spinner fa-spin custom-btn-small display_none"></i><span
                            id="spinnerText">{{ __('Update') }}</span></button>
                </div>
            </div>
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
        var rowNo = '{{ $rowNo+1 }}';
        var editStack = '{{ json_encode($stack) }}';
        var shippingProviderUrl = '{{ route('search.shipping.providers') }}';
    </script>
    <script src="{{ asset('Modules/Inventory/Resources/assets/js/transfer.min.js?v=3.3') }}"></script>

@endsection
