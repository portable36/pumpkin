@extends('layouts.app')

@section('title', 'My Addresses')

@section('content')
<div class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3 style="margin-bottom: 1rem;">{{ auth()->user()->name }}</h3>
            <ul class="sidebar-menu">
                <li><a href="/dashboard">Overview</a></li>
                <li><a href="/dashboard/orders">Orders</a></li>
                <li><a href="/dashboard/wishlist">Wishlist</a></li>
                <li><a href="/dashboard/reviews">Reviews</a></li>
                <li><a href="/dashboard/settings">Settings</a></li>
                <li><a href="/dashboard/addresses" class="active">Addresses</a></li>
            </ul>
            <hr style="margin: 1rem 0;">
            <p style="font-size: 0.9rem; color: #666;">
                <strong>Email:</strong> {{ auth()->user()->email }}<br>
                <strong>Phone:</strong> {{ auth()->user()->phone }}<br>
                <strong>Joined:</strong> {{ auth()->user()->created_at->format('M d, Y') }}
            </p>
        </div>

        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h2 style="margin-bottom: 0.5rem;">My Addresses</h2>
                    <p style="color: #666;">Manage your shipping and billing addresses</p>
                </div>
                <button class="btn" onclick="document.getElementById('addAddressModal').classList.add('active')">+ Add New Address</button>
            </div>

            <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
                @forelse($addresses as $address)
                    <div class="card" style="position: relative;">
                        @if($address->is_default)
                            <div style="position: absolute; top: 1rem; right: 1rem;">
                                <span class="badge badge-success">Default</span>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <div style="margin-bottom: 1rem;">
                                <strong style="font-size: 1.1rem; display: block; margin-bottom: 0.5rem;">{{ $address->label ?? 'Address' }}</strong>
                                @if($address->type)
                                    <span class="badge" style="background: #e0e0e0; color: #333;">{{ ucfirst($address->type) }}</span>
                                @endif
                            </div>
                            
                            <div style="color: #666; line-height: 1.8; margin-bottom: 1.5rem;">
                                @if($address->recipient_name)
                                    <strong style="display: block; color: #333;">{{ $address->recipient_name }}</strong>
                                @endif
                                {{ $address->address_line1 }}<br>
                                @if($address->address_line2)
                                    {{ $address->address_line2 }}<br>
                                @endif
                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                {{ $address->country }}<br>
                                @if($address->phone)
                                    <span style="display: block; margin-top: 0.5rem;">📞 {{ $address->phone }}</span>
                                @endif
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @if(!$address->is_default)
                                    <form action="/dashboard/addresses/{{ $address->id }}/set-default" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #28a745; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Set as Default</button>
                                    </form>
                                @endif
                                <button onclick="editAddress({{ $address->id }})" style="background: #007bff; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Edit</button>
                                @if(!$address->is_default)
                                    <form action="/dashboard/addresses/{{ $address->id }}/delete" method="POST" style="display: inline;" onsubmit="return confirm('Delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📍</div>
                        <h3 style="margin-bottom: 1rem;">No Addresses Saved</h3>
                        <p style="color: #666; margin-bottom: 2rem;">Add your shipping and billing addresses for faster checkout</p>
                        <button class="btn" onclick="document.getElementById('addAddressModal').classList.add('active')">Add Your First Address</button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div id="addAddressModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>Add New Address</h3>
            <button onclick="document.getElementById('addAddressModal').classList.remove('active')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999;">&times;</button>
        </div>
        
        <form method="POST" action="/dashboard/addresses/create">
            @csrf
            
            <div class="form-group">
                <label for="label">Address Label *</label>
                <input type="text" id="label" name="label" placeholder="e.g., Home, Office, Apartment" required>
            </div>
            
            <div class="form-group">
                <label for="type">Address Type *</label>
                <select id="type" name="type" required>
                    <option value="shipping">Shipping</option>
                    <option value="billing">Billing</option>
                    <option value="both">Both</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="recipient_name">Recipient Name</label>
                <input type="text" id="recipient_name" name="recipient_name" placeholder="Full name">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="+1 (555) 123-4567">
            </div>
            
            <div class="form-group">
                <label for="address_line1">Address Line 1 *</label>
                <input type="text" id="address_line1" name="address_line1" placeholder="Street address" required>
            </div>
            
            <div class="form-group">
                <label for="address_line2">Address Line 2</label>
                <input type="text" id="address_line2" name="address_line2" placeholder="Apartment, suite, unit, etc.">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" id="city" name="city" required>
                </div>
                
                <div class="form-group">
                    <label for="state">State/Province *</label>
                    <input type="text" id="state" name="state" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="postal_code">Postal Code *</label>
                    <input type="text" id="postal_code" name="postal_code" required>
                </div>
                
                <div class="form-group">
                    <label for="country">Country *</label>
                    <input type="text" id="country" name="country" value="United States" required>
                </div>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_default" value="1">
                    <span>Set as default address</span>
                </label>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn">Save Address</button>
                <button type="button" onclick="document.getElementById('addAddressModal').classList.remove('active')" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function editAddress(id) {
    // This would open an edit modal - implement as needed
    alert('Edit address functionality would be implemented here for address ID: ' + id);
}
</script>
@endsection
