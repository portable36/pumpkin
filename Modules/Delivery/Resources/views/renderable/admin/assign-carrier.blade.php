<div class="d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-6">
                <div class="card text-dark bg-light mb-3">
                    <h5 class="card-header  bg-dark text-white py-3">{{ __('Order') }}</h5>
                    <div class="card-body">
                        <div class="d-flex row py-2">
                            <div class="col-8">
                                <h6 class="card-text">{{ __('Deliverable') }}:</h6>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-info ms-2">{{ __('Yes/No') }}</span>
                            </div>
                        </div>
                        <div class="d-flex row py-2">
                            <div class="col-8">
                                <h6 class="card-text">{{ __('Assigned') }}:</h6>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-info ms-2">
                                    @if(isset($carrierOrder))
                                        {{ ('Yes') }}
                                    @else
                                        {{ ('No') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card text-dark bg-light mb-3">
                    <h5 class="card-header bg-dark text-white py-3">{{ __('Assignee') }}</h5>
                    <div class="card-body">
                        <div class="d-flex row py-2">
                            <div class="col-4">
                                <h6 class="card-text">{{ __('Unique ID') }}:</h6>
                            </div>
                            <div class="col-8">
                                {{ $carrierOrder?->deliveryMan?->unique_id ?? __('N/A') }}
                            </div>
                        </div>
                        <div class="d-flex row py-2">
                            <div class="col-4">
                                <h6 class="card-text">{{ __('Name') }}:</h6>
                            </div>
                            <div class="col-8">
                                {{ $carrierOrder?->deliveryMan?->user?->name ?? __('N/A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card text-dark bg-light mb-3">
                <h5 class="card-header bg-warning py-3">{{ __('Assign') }}</h5>
                <div class="card-body">
                    <div class="row" id="carrier-modal-alert">

                    </div>
                    <div class="row align-items-center">
                        <div class="col-2 text-right">
                            <span class="ps-2">{{ __('Search') }}:</span>
                        </div>
                        <div class="col-9">
                            <input type="text" name="search_carrier" id="search_carrier" class="form-control" placeholder="Type name or unique id">
                        </div>
                        <div class="col-1">
                            <div class="spinner-grow text-success d-none" role="status" id="carrier_search_spinner">
                                <span class="visually-hidden">{{ __('Loading...') }}</span>
                            </div>
                        </div>
                    </div>
                    <hr class="hr" />
                    <table class="table table-striped table-bordered table-hover table-sm" id="carrier_table">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="text-center">{{ __('Unique ID') }}</th>
                                <th scope="col" class="text-center">{{ __('Name') }}</th>
                                <th scope="col" class="text-center">{{ __('Assigned Orders') }}</th>
                                <th scope="col" class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($carriers as $carrier)
                                <tr>
                                    <td class="text-center">{{ $carrier->unique_id }}</td>
                                    <td class="text-center">{{ optional($carrier->user)->name }}</td>
                                    <td class="text-center">{{ optional($carrier->orders)->whereIn('order_status_id', getOrderStatusIds(['assigned', 'pickup']))->count() }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm assign-carrier-btn" data-id="{{ $carrier->id }}">
                                            <span class="fa fa-plus"></span>
                                            <span class="fa fa-solid fa-cog fa-spin d-none"></span>
                                            <span class="ps-1">{{ __('Assign') }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td></td>
                                    <td>{{ __('No record found!') }}</td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
