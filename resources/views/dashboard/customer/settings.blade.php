@extends('layouts.app')

@section('title', 'Account Settings')

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
                <li><a href="/dashboard/settings" class="active">Settings</a></li>
                <li><a href="/dashboard/addresses">Addresses</a></li>
            </ul>
            <hr style="margin: 1rem 0;">
            <p style="font-size: 0.9rem; color: #666;">
                <strong>Email:</strong> {{ auth()->user()->email }}<br>
                <strong>Phone:</strong> {{ auth()->user()->phone }}<br>
                <strong>Joined:</strong> {{ auth()->user()->created_at->format('M d, Y') }}
            </p>
        </div>

        <div class="main-content">
            <h2>Account Settings</h2>
            <p style="color: #666; margin-bottom: 2rem;">Manage your account information and preferences</p>

            <form method="POST" action="{{ route('dashboard.settings.update') }}">
                @csrf
                
                <div style="background: #f9f9f9; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Personal Information</h3>
                    
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <p style="color: #dc3545; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <p style="color: #dc3545; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+1 (555) 123-4567">
                        @error('phone')
                            <p style="color: #dc3545; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="background: #f9f9f9; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Change Password</h3>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">Leave blank if you don't want to change your password</p>
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter new password (minimum 6 characters)">
                        @error('password')
                            <p style="color: #dc3545; font-size: 0.9rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password">
                    </div>
                </div>

                <div style="background: #f9f9f9; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Account Information</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <p style="font-size: 0.9rem; color: #999; margin-bottom: 0.25rem;">Account Type</p>
                            <strong>
                                @if(auth()->user()->is_admin)
                                    Administrator
                                @elseif(auth()->user()->is_vendor)
                                    Vendor
                                @else
                                    Customer
                                @endif
                            </strong>
                        </div>
                        <div>
                            <p style="font-size: 0.9rem; color: #999; margin-bottom: 0.25rem;">Member Since</p>
                            <strong>{{ auth()->user()->created_at->format('F d, Y') }}</strong>
                        </div>
                        <div>
                            <p style="font-size: 0.9rem; color: #999; margin-bottom: 0.25rem;">Total Orders</p>
                            <strong>{{ auth()->user()->orders->count() }}</strong>
                        </div>
                        <div>
                            <p style="font-size: 0.9rem; color: #999; margin-bottom: 0.25rem;">Total Spent</p>
                            <strong>${{ number_format(auth()->user()->orders->sum('total_amount'), 2) }}</strong>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="/dashboard" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-weight: 600;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
