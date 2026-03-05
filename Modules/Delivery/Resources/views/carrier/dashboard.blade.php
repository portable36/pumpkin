@extends('delivery::layouts.app')
@section('page_title', __('Carrier Dashboard'))
@section('css')

@endsection
@section('content')
<div class="row">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-block">
                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-donate f-30 text-c-yellow rides-icon neg-transition-scale-svg"></i>
                    </div>
                    <div class="col">
                        <h3 class="font-weight-500">{{ formatNumber($balance) }}
                            
                        </h3>
                        <span class="d-block text-uppercase font-weight-600 c-gray-5">{{ __('Balance') }} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-block">
                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-donate f-30 text-c-yellow rides-icon neg-transition-scale-svg"></i>
                    </div>
                    <div class="col">
                        <h3 class="font-weight-500">{{ formatNumber($earning) }}
                            
                        </h3>
                        <span class="d-block text-uppercase font-weight-600 c-gray-5">{{ __('Total Earning') }} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-block">
                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <i class="feather icon-stop-circle f-30 text-c-yellow neg-transition-scale-svg "></i>
                    </div>
                    <div class="col">
                        <h3 class="font-weight-500">{{ $totalCompletedOrder }}
                            
                        </h3>
                        {{ __('Completed Delivery') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-block">
                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <i class="feather icon-shopping-cart f-30 text-c-yellow neg-transition-scale-svg"></i>
                    </div>
                    <div class="col">
                        <h3 class="font-weight-500">{{ $totalPickupOrder }}
                            
                        </h3>
                        {{ __('Pickup') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-block">
                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <i class="feather icon-stop-circle f-30 text-c-yellow neg-transition-scale-svg "></i>
                    </div>
                    <div class="col">
                        <h3 class="font-weight-500">{{ $totalDeliveredOrder }}  
                        </h3>
                        {{ __('Delivered') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-block">
                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <i class="feather icon-stop-circle f-30 text-c-yellow neg-transition-scale-svg "></i>
                    </div>
                    <div class="col">
                        <h3 class="font-weight-500">{{ $totalAssignOrder }}  
                        </h3>
                        {{ __('Assigned') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

