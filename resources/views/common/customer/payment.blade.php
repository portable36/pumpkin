@extends($from . '.layouts.app')
@section('page_title', __('Customer Payment'))
@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/Inventory/Resources/assets/css/payment.min.css') }}">
@endsection
@section('content')
    <!-- Main content -->
    <div class="list-container" id="customer-payment-container">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="mb-0">{{ __(':x Payment', ['x' => $customer?->name]) }}</h5>
            </div>

            <div class="ms-3">
                @include('common.customer.top-menu', ['customer' => $customer, 'from' => $from, 'menuName' => 'payment'])
            </div>
            @php
                $paymentRoute = $from == 'vendor' ? 'vendor.customer.paymentStore' : 'customer.paymentStore';
                $paymentRoute = route($paymentRoute, $customer->id);
            @endphp

            <form action="{{ $paymentRoute }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
                <input type="hidden" name="payment_date" value="{{ request('payment_date') }}">
                <input type="hidden" name="description" value="{{ request('description') }}">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="payment-table">
                        <thead>
                            <tr>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Order Date') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Paid Amount') }}</th>
                                <th>{{ __('Balance') }}</th>
                                <th>{{ __('Payment Status') }}</th>
                                <th class="text-end">{{ __('Amount to Pay') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        @php
                                            $orderRoute = $from == 'vendor' ? 'vendorOrder.view' : 'order.view';
                                            $orderRoute = route($orderRoute, ['id' => $order->id]);
                                        @endphp
                                        <a href="{{ $orderRoute }}" class="text-decoration-none">
                                            {{ $order->reference }}
                                        </a>
                                    </td>
                                    <td>{{ timeZoneFormatDate($order->order_date) }}</td>
                                    <td>{{ formatNumber($order->total) }}</td>
                                    <td>{{ formatNumber($order->paid) }}</td>
                                    <td>
                                        <strong class="text-{{ ($order->total - $order->paid) > 0 ? 'danger' : 'success' }}">
                                            {{ formatNumber($order->total - $order->paid) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @php
                                            $status = __('Unpaid');
                                            $badgeClass = 'badge bg-danger';
                                            if ($order->paid >= $order->total) {
                                                $status = __('Paid');
                                                $badgeClass = 'badge bg-success';
                                            } elseif ($order->paid > 0) {
                                                $status = __('Partially Paid');
                                                $badgeClass = 'badge bg-warning';
                                            }
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="input-wrapper d-inline-block" style="width: 140px;">
                                            <input type="number" 
                                                name="orders[{{ $order->id }}]" 
                                                class="form-control amount-input" 
                                                step="0.0000001" 
                                                min="0" 
                                                max="{{ $order->total - $order->paid }}" 
                                                placeholder="0.00"
                                                value="0"
                                                data-balance="{{ $order->total - $order->paid }}" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('No orders found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($orders) > 0)
                        <tfoot>
                            <tr class="border-top">
                                <td colspan="6" class="text-end fw-bold"></td>
                                <td class="text-end">
                                    <span class="fw-bold fs-5 text-dark">{{ __('Total') }}:</span>
                                    <span class="fw-bold fs-5" id="total-payment-amount">0.00</span>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                @if(count($orders) > 0)
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather icon-credit-card me-1"></i>{{ __('Process Payment') }}
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @php
        // Get amount_paid from URL and ensure it's a valid number
        $amountPaid = request('amount_paid', 0);
        // Convert to float and ensure it's a valid number
        $amountPaid = is_numeric($amountPaid) ? (float) $amountPaid : 0;
        // Ensure it's not negative
        $amountPaid = max(0, $amountPaid);
    @endphp
@endsection

@section('js')
    <script>
        var amountPaid = {{ $amountPaid }};
    </script>
    <script src="{{ asset('Modules/Inventory/Resources/assets/js/payment.min.js') }}"></script>
@endsection
