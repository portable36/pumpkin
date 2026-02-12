@extends('layouts.app')

@section('title', 'Checkout - Pumpkin')

@section('content')
<div class="container" style="margin: 2rem 0;">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
        <div>
            <h1>Checkout</h1>
            
            <form action="/orders/create" method="POST">
                @csrf
                
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-body">
                        <h3>Shipping Address</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" name="shipping_first_name" value="{{ old('shipping_first_name', auth()->user()->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" name="shipping_last_name" value="{{ old('shipping_last_name') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Phone *</label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Address *</label>
                            <input type="text" name="shipping_address" placeholder="Street address" value="{{ old('shipping_address') }}" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required>
                            </div>
                            <div class="form-group">
                                <label>State *</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Zip Code *</label>
                                <input type="text" name="shipping_zip" value="{{ old('shipping_zip') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Country *</label>
                            <input type="text" name="shipping_country" value="{{ old('shipping_country', 'United States') }}" required>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-body">
                        <h3>Billing Address</h3>
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" id="same_as_shipping" name="same_as_shipping" checked>
                            Same as shipping address
                        </label>
                        <div id="billing-form" style="display: none; margin-top: 1rem;">
                            <div class="form-group">
                                <label>Address *</label>
                                <input type="text" name="billing_address" value="{{ old('billing_address') }}">
                            </div>
                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>City *</label>
                                    <input type="text" name="billing_city" value="{{ old('billing_city') }}">
                                </div>
                                <div class="form-group">
                                    <label>State *</label>
                                    <input type="text" name="billing_state" value="{{ old('billing_state') }}">
                                </div>
                                <div class="form-group">
                                    <label>Zip Code *</label>
                                    <input type="text" name="billing_zip" value="{{ old('billing_zip') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-body">
                        <h3>Shipping Method</h3>
                        <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; margin-bottom: 0.5rem;">
                            <input type="radio" name="shipping_method" value="standard" checked>
                            <div style="flex: 1;">
                                <strong>Standard Shipping</strong>
                                <p style="color: #666; font-size: 0.9rem;">Delivery in 5-7 business days</p>
                            </div>
                            <strong>$9.99</strong>
                        </label>
                        <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; margin-bottom: 0.5rem;">
                            <input type="radio" name="shipping_method" value="express">
                            <div style="flex: 1;">
                                <strong>Express Shipping</strong>
                                <p style="color: #666; font-size: 0.9rem;">Delivery in 2-3 business days</p>
                            </div>
                            <strong>$24.99</strong>
                        </label>
                        <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                            <input type="radio" name="shipping_method" value="overnight">
                            <div style="flex: 1;">
                                <strong>Overnight Shipping</strong>
                                <p style="color: #666; font-size: 0.9rem;">Delivery next business day</p>
                            </div>
                            <strong>$49.99</strong>
                        </label>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-body">
                        <h3>Payment Method</h3>
                        <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; margin-bottom: 0.5rem;">
                            <input type="radio" name="payment_method" value="credit_card" checked>
                            <strong>Credit Card</strong>
                        </label>
                        <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; margin-bottom: 0.5rem;">
                            <input type="radio" name="payment_method" value="paypal">
                            <strong>PayPal</strong>
                        </label>
                        <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                            <input type="radio" name="payment_method" value="bank_transfer">
                            <strong>Bank Transfer</strong>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="agree_terms" required>
                        I agree to the terms and conditions
                    </label>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Place Order</button>
            </form>
        </div>

        <div>
            <div class="card" style="position: sticky; top: 100px;">
                <div class="card-body">
                    <h3>Order Summary</h3>
                    <div style="margin: 1.5rem 0; font-size: 0.9rem;">
                        @foreach($cart_items as $item)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">
                                <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                <strong>${{ number_format($item->product->price * $item->quantity, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <hr>
                    <div style="margin: 1rem 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Subtotal:</span>
                            <strong>${{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Shipping:</span>
                            <strong>$9.99</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Tax:</span>
                            <strong>${{ number_format($tax, 2) }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: bold;">
                        <span>Total:</span>
                        <span class="card-price">${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('same_as_shipping').addEventListener('change', function() {
    document.getElementById('billing-form').style.display = this.checked ? 'none' : 'block';
});
</script>
@endsection
