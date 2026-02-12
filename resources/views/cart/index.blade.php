@extends('layouts.app')

@section('title', 'Shopping Cart - Pumpkin')

@section('content')
<div class="container" style="margin: 2rem 0;">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($items->count() > 0)
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
            <div>
                <h1>Shopping Cart</h1>
                <div style="background: white; border-radius: 8px; border: 1px solid #ddd;">
                    <table style="width: 100%;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd;">
                                <th style="padding: 1rem; text-align: left;">Product</th>
                                <th style="padding: 1rem; text-align: center;">Price</th>
                                <th style="padding: 1rem; text-align: center;">Quantity</th>
                                <th style="padding: 1rem; text-align: center;">Total</th>
                                <th style="padding: 1rem; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;">
                                        <strong>{{ $item->product->name }}</strong>
                                        <p style="color: #666; font-size: 0.9rem;">SKU: {{ $item->product->sku }}</p>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">${{ number_format($item->product->price, 2) }}</td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <form action="/cart/update" method="POST" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                            @csrf
                                            <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                            <button type="button" onclick="decreaseQty(this)">−</button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" style="width: 50px; text-align: center;">
                                            <button type="button" onclick="increaseQty(this)">+</button>
                                        </form>
                                    </td>
                                    <td style="padding: 1rem; text-align: center; font-weight: bold;">
                                        ${{ number_format($item->product->price * $item->quantity, 2) }}
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <form action="/cart/remove" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                            <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem;">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="card" style="position: sticky; top: 100px;">
                    <div class="card-body">
                        <h3>Order Summary</h3>
                        <div style="margin: 1.5rem 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                <span>Subtotal:</span>
                                <strong>${{ number_format($subtotal, 2) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                <span>Shipping:</span>
                                <strong>${{ number_format($shipping, 2) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                <span>Tax:</span>
                                <strong>${{ number_format($tax, 2) }}</strong>
                            </div>
                            @if($coupon_discount > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #28a745;">
                                    <span>Discount:</span>
                                    <strong>-${{ number_format($coupon_discount, 2) }}</strong>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <div style="display: flex; justify-content: space-between; margin: 1.5rem 0; font-size: 1.3rem; font-weight: bold;">
                            <span>Total:</span>
                            <span class="card-price">${{ number_format($total, 2) }}</span>
                        </div>

                        <form action="/cart/apply-coupon" method="POST" style="margin-bottom: 1rem;">
                            @csrf
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="text" name="coupon_code" placeholder="Coupon code" style="flex: 1;">
                                <button type="submit" class="btn btn-outline">Apply</button>
                            </div>
                        </form>

                        <a href="/checkout" class="btn" style="width: 100%; text-align: center; display: block;">Proceed to Checkout</a>
                        <a href="/shop" class="btn btn-outline" style="width: 100%; text-align: center; display: block; margin-top: 0.5rem;">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🛒</div>
            <h2>Your cart is empty</h2>
            <p style="color: #666; margin: 1rem 0;">Add some products to get started!</p>
            <a href="/shop" class="btn" style="display: inline-block; margin-top: 1rem;">Continue Shopping</a>
        </div>
    @endif
</div>

<script>
function increaseQty(btn) {
    const input = btn.previousElementSibling;
    input.value = parseInt(input.value) + 1;
    input.form.submit();
}

function decreaseQty(btn) {
    const input = btn.nextElementSibling;
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
        input.form.submit();
    }
}
</script>
@endsection
