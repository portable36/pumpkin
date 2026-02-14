@extends('layouts.app')

@section('title', 'Settings - Admin Dashboard')

@section('content')
<div style="display: flex; min-height: calc(100vh - 80px);">
    <!-- Sidebar -->
    <div style="width: 250px; background: #f8f9fa; border-right: 1px solid #ddd; padding: 2rem 0;">
        <nav style="display: flex; flex-direction: column;">
            <a href="/admin/dashboard" style="padding: 1rem; color: #333; text-decoration: none;">📊 Dashboard</a>
            <a href="/admin/users" style="padding: 1rem; color: #333; text-decoration: none;">👥 Users</a>
            <a href="/admin/vendors" style="padding: 1rem; color: #333; text-decoration: none;">🏪 Vendors</a>
            <a href="/admin/products" style="padding: 1rem; color: #333; text-decoration: none;">📦 Products</a>
            <a href="/admin/orders" style="padding: 1rem; color: #333; text-decoration: none;">🛒 Orders</a>
            <a href="/admin/settings" style="padding: 1rem; border-left: 3px solid #667eea; color: #667eea; text-decoration: none; font-weight: bold;">⚙️ Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1; padding: 2rem; overflow-y: auto;">
        <h1>Platform Settings</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/admin/settings" method="POST">
            @csrf

            <!-- General Settings -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h2 style="margin-top: 0;">General Settings</h2>
                    
                    <div class="form-group">
                        <label>App Name</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'Pumpkin' }}" required>
                    </div>

                    <div class="form-group">
                        <label>App Tagline</label>
                        <input type="text" name="app_tagline" value="{{ $settings['app_tagline'] ?? '' }}" required>
                    </div>

                    <div class="form-group">
                        <label>Admin Email</label>
                        <input type="email" name="admin_email" value="{{ $settings['admin_email'] ?? '' }}" required>
                    </div>

                    <div class="form-group">
                        <label>Support Email</label>
                        <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}" required>
                    </div>

                    <div class="form-group">
                        <label>Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '$' }}" required maxlength="5">
                    </div>

                    <div class="form-group">
                        <label>Timezone</label>
                        <select name="timezone" required>
                            <option value="America/New_York" {{ ($settings['timezone'] ?? 'America/New_York') === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ ($settings['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ ($settings['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ ($settings['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Commission & Tax Settings -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h2 style="margin-top: 0;">Commission & Tax</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Vendor Commission Rate (%)</label>
                            <input type="number" name="commission_rate" value="{{ $settings['commission_rate'] ?? '15' }}" min="0" max="100" step="0.1" required>
                        </div>

                        <div class="form-group">
                            <label>Tax Rate (%)</label>
                            <input type="number" name="tax_rate" value="{{ $settings['tax_rate'] ?? '10' }}" min="0" max="100" step="0.1" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Settings -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h2 style="margin-top: 0;">Shipping Costs</h2>
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div class="form-group">
                            <label>Standard Shipping ($)</label>
                            <input type="number" name="standard_shipping" value="{{ $settings['standard_shipping'] ?? '9.99' }}" min="0" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label>Express Shipping ($)</label>
                            <input type="number" name="express_shipping" value="{{ $settings['express_shipping'] ?? '24.99' }}" min="0" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label>Overnight Shipping ($)</label>
                            <input type="number" name="overnight_shipping" value="{{ $settings['overnight_shipping'] ?? '49.99' }}" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Logic Settings -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h2 style="margin-top: 0;">Business Logic</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>
                                <strong>Require Vendor Approval?</strong>
                                <select name="vendor_approval_required" required>
                                    <option value="yes" {{ ($settings['vendor_approval_required'] ?? 'yes') === 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ ($settings['vendor_approval_required'] ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <strong>Require Product Approval?</strong>
                                <select name="product_approval_required" required>
                                    <option value="yes" {{ ($settings['product_approval_required'] ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ ($settings['product_approval_required'] ?? 'no') === 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #ddd; margin: 1.5rem 0; padding-top: 1.5rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>
                                    <strong>Enable User Registration?</strong>
                                    <select name="user_registration_enabled" required>
                                        <option value="yes" {{ ($settings['user_registration_enabled'] ?? 'yes') === 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ ($settings['user_registration_enabled'] ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </label>
                            </div>

                            <div class="form-group">
                                <label>
                                    <strong>Enable Vendor Registration?</strong>
                                    <select name="vendor_registration_enabled" required>
                                        <option value="yes" {{ ($settings['vendor_registration_enabled'] ?? 'yes') === 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ ($settings['vendor_registration_enabled'] ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #ddd; margin: 1.5rem 0; padding-top: 1.5rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Max Products Per Vendor</label>
                                <input type="number" name="max_products_per_vendor" value="{{ $settings['max_products_per_vendor'] ?? '10000' }}" min="1" required>
                            </div>

                            <div class="form-group">
                                <label>Minimum Order Amount ($)</label>
                                <input type="number" name="min_order_amount" value="{{ $settings['min_order_amount'] ?? '0' }}" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Settings -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h2 style="margin-top: 0;">Payment Methods</h2>
                    
                    <div class="form-group">
                        <label>Enabled Payment Methods (comma-separated)</label>
                        <input type="text" name="payment_methods" value="{{ $settings['payment_methods'] ?? 'credit_card,paypal,bank_transfer' }}" required>
                        <small style="color: #666;">Example: credit_card,paypal,bank_transfer,stripe</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 2rem;">
                <button type="submit" class="btn" style="padding: 1rem 2rem;">Save Settings</button>
                <a href="/admin/dashboard" class="btn btn-outline" style="padding: 1rem 2rem; text-decoration: none;">Cancel</a>
            </div>
        </form>

        <!-- Settings Documentation -->
        <div class="card" style="background: #f0f7ff; border: 1px solid #667eea;">
            <div class="card-body">
                <h3 style="margin-top: 0; color: #667eea;">💡 Settings Guide</h3>
                <ul style="margin: 0; padding-left: 1.5rem;">
                    <li><strong>Vendor Approval:</strong> When enabled, new vendors must be approved by admin before selling</li>
                    <li><strong>Commission Rate:</strong> Percentage of each sale that the platform takes from vendors</li>
                    <li><strong>Tax Rate:</strong> Applied to all orders automatically</li>
                    <li><strong>Product Approval:</strong> When enabled, all new products must be reviewed before going live</li>
                    <li><strong>Registration Control:</strong> Disable registration to restrict new user/vendor signups</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: Segoe UI, system-ui;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group small {
    display: block;
    margin-top: 0.25rem;
}
</style>
@endsection
