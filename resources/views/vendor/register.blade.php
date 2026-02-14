@extends('layouts.app')

@section('title', 'Become a Vendor - Pumpkin')

@section('content')
<div class="container" style="margin: 2rem 0;">
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="text-align: center; margin-bottom: 0.5rem;">Become a Vendor</h1>
        <p style="text-align: center; color: #666; margin-bottom: 2rem;">Start selling on Pumpkin Marketplace today</p>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="/vendor/register" method="POST" class="card" style="padding: 2rem;">
            @csrf

            <h3 style="margin-top: 0;">Personal Information</h3>

            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>Phone Number *</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required>
            </div>

            <hr style="margin: 2rem 0;">

            <h3>Shop Information</h3>

            <div class="form-group">
                <label>Shop Name *</label>
                <input type="text" name="shop_name" placeholder="Your unique shop name" value="{{ old('shop_name') }}" required>
                <small style="color: #666;">This will be displayed to customers</small>
            </div>

            <div class="form-group">
                <label>Shop Description *</label>
                <textarea name="shop_description" rows="4" placeholder="Describe your shop and what you sell" required>{{ old('shop_description') }}</textarea>
            </div>

            <hr style="margin: 2rem 0;">

            <h3>Account Security</h3>

            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required>
                <small style="color: #666;">Minimum 8 characters</small>
            </div>

            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="password_confirmation" required>
            </div>

            <hr style="margin: 2rem 0;">

            <div class="alert" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <h4 style="margin-top: 0;">Benefits of Becoming a Vendor</h4>
                <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                    <li>Free access to Pumpkin marketplace</li>
                    <li>Detailed sales analytics</li>
                    <li>Automated order processing</li>
                    <li>Secure payment system</li>
                    <li>24/7 customer support</li>
                </ul>
            </div>

            <div class="alert" style="background: #fff3cd; border: 1px solid #ffc107; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <strong>⚠️ Important:</strong> Your vendor account will require admin approval before you can start selling. We review all new vendors to ensure quality and compliance with our policies.
            </div>

            <label style="display: flex; align-items: start; gap: 0.5rem; margin-bottom: 1.5rem;">
                <input type="checkbox" name="terms" required style="margin-top: 0.5rem;">
                <span>
                    I agree to the <strong>Pumpkin Vendor Terms and Conditions</strong> and understand that my account will be reviewed by an administrator before activation. *
                </span>
            </label>

            <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Create Vendor Account</button>

            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="/login" style="color: #667eea; text-decoration: none; font-weight: bold;">Login here</a>
            </p>
        </form>

        <!-- Info Box -->
        <div class="card" style="margin-top: 2rem; padding: 1.5rem;">
            <h4 style="margin-top: 0;">How Does It Work?</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📝</div>
                    <strong>1. Register</strong>
                    <p style="color: #666; font-size: 0.9rem;">Fill out vendor details</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">✅</div>
                    <strong>2. Get Approved</strong>
                    <p style="color: #666; font-size: 0.9rem;">Admin verifies your account</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🛍️</div>
                    <strong>3. Add Products</strong>
                    <p style="color: #666; font-size: 0.9rem;">Start listing your items</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">💰</div>
                    <strong>4. Earn Money</strong>
                    <p style="color: #666; font-size: 0.9rem;">Get paid for sales</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
