@extends($from . '.layouts.app')
@section('page_title', __('Supplier Payment'))
@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/Inventory/Resources/assets/css/payment.min.css') }}">
@endsection
@section('content')
    <!-- Main content -->
    <div class="list-container" id="supplier-payment-container">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="mb-0">{{ __(':x Payment', ['x' => $supplier?->name]) }}</h5>
            </div>

            <div class="ms-3">
                @include('inventory::common.supplier-menu', ['from' => $from])
            </div>
            @php
                $paymentRoute = $from == 'vendor' ? 'vendor.supplier.paymentStore' : 'supplier.paymentStore';
                $paymentRoute = route($paymentRoute, $supplier->id);
            @endphp

            <form action="{{ $paymentRoute }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
                <input type="hidden" name="payment_date" value="{{ request('payment_date') }}">
                <input type="hidden" name="description" value="{{ request('description') }}">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="payment-table">
                        <thead>
                            <tr>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Purchase Date') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Paid Amount') }}</th>
                                <th>{{ __('Balance') }}</th>
                                <th>{{ __('Payment Status') }}</th>
                                <th class="text-end">{{ __('Amount to Pay') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>
                                        @php
                                            $purchaseRoute = $from == 'vendor' ? 'vendor.purchase.view' : 'purchase.view';
                                            $purchaseRoute = route($purchaseRoute, ['id' => $purchase->id]);
                                        @endphp
                                        <a href="{{ $purchaseRoute }}" class="text-decoration-none">
                                            {{ $purchase->reference }}
                                        </a>
                                    </td>
                                    <td>{{ timeZoneFormatDate($purchase->purchase_date) }}</td>
                                    <td>{{ formatNumber($purchase->total_amount) }}</td>
                                    <td>{{ formatNumber($purchase->paid_amount) }}</td>
                                    <td>
                                        <strong class="text-{{ ($purchase->total_amount - $purchase->paid_amount) > 0 ? 'danger' : 'success' }}">
                                            {{ formatNumber($purchase->total_amount - $purchase->paid_amount) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @php
                                            $status = __('Unpaid');
                                            $badgeClass = 'badge bg-danger';
                                            if ($purchase->paid_amount >= $purchase->total_amount) {
                                                $status = __('Paid');
                                                $badgeClass = 'badge bg-success';
                                            } elseif ($purchase->paid_amount > 0) {
                                                $status = __('Partially Paid');
                                                $badgeClass = 'badge bg-warning';
                                            }
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="input-wrapper d-inline-block" style="width: 140px;">
                                            <input type="number" 
                                                name="amounts[{{ $purchase->id }}]" 
                                                class="form-control amount-input" 
                                                step="0.0000001" 
                                                min="0" 
                                                max="{{ $purchase->total_amount - $purchase->paid_amount }}" 
                                                placeholder="0.00"
                                                value="0"
                                                data-balance="{{ $purchase->total_amount - $purchase->paid_amount }}" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('No purchase orders found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($purchases) > 0)
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

                @if(count($purchases) > 0)
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
