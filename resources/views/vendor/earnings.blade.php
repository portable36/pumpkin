@extends('layouts.app')

@section('title', 'Earnings - Pumpkin Vendor')

@section('content')
<div style="display: flex; min-height: calc(100vh - 80px);">
    <!-- Sidebar -->
    <div style="width: 250px; background: #f8f9fa; border-right: 1px solid #ddd; padding: 2rem 0;">
        <nav style="display: flex; flex-direction: column;">
            <a href="/vendor/dashboard" style="padding: 1rem; color: #333; text-decoration: none;">📊 Dashboard</a>
            <a href="/vendor/products" style="padding: 1rem; color: #333; text-decoration: none;">📦 Products</a>
            <a href="/vendor/orders" style="padding: 1rem; color: #333; text-decoration: none;">🛒 Orders</a>
            <a href="/vendor/earnings" style="padding: 1rem; border-left: 3px solid #667eea; color: #667eea; text-decoration: none;">💰 Earnings</a>
            <a href="/vendor/reviews" style="padding: 1rem; color: #333; text-decoration: none;">⭐ Reviews</a>
            <a href="/vendor/settings" style="padding: 1rem; color: #333; text-decoration: none;">⚙️ Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1; padding: 2rem;">
        <h1>Earnings & Payouts</h1>

        <!-- Stats Cards -->
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); margin: 2rem 0;">
            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Total Earnings (All Time)</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0; color: #28a745;">${{ number_format($totalEarnings, 2) }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Available Balance</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0; color: #667eea;">${{ number_format($availableBalance, 2) }}</p>
                    <button class="btn" style="margin-top: 1rem; width: 100%;">Withdraw</button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">This Month</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">${{ number_format($monthlyEarnings, 2) }}</p>
                    <p style="color: #28a745; font-size: 0.9rem;">+12% from last month</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Pending Payouts</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{{ $pendingPayouts }}</p>
                    <p style="color: #ffc107; font-size: 0.9rem;">Processing: ${{ number_format($processingAmount, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Earnings Chart -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <h3>Earnings Over Time</h3>
                <p style="color: #999; text-align: center; padding: 3rem;">📊 Chart visualization coming soon</p>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <h3>Recent Transactions</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd;">
                                <th style="padding: 1rem; text-align: left;">Date</th>
                                <th style="padding: 1rem; text-align: left;">Description</th>
                                <th style="padding: 1rem; text-align: right;">Amount</th>
                                <th style="padding: 1rem; text-align: center;">Type</th>
                                <th style="padding: 1rem; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;">{{ $transaction->created_at->format('M d, Y') }}</td>
                                    <td style="padding: 1rem;">{{ $transaction->description }}</td>
                                    <td style="padding: 1rem; text-align: right; font-weight: bold;">
                                        <span style="color: {{ $transaction->type === 'credit' ? '#28a745' : '#dc3545' }};">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">{{ ucfirst($transaction->type) }}</td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <span class="badge {{ $transaction->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 2rem; text-align: center; color: #999;">No transactions yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payout Methods -->
        <div class="card">
            <div class="card-body">
                <h3>Payout Methods</h3>
                <div style="margin: 1rem 0;">
                    <div style="padding: 1rem; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Bank Account</strong>
                            <p style="color: #666; font-size: 0.9rem; margin: 0.25rem 0;">Account ending in ****1234</p>
                        </div>
                        <a href="#" class="btn btn-small btn-outline">Edit</a>
                    </div>
                </div>
                <button class="btn btn-outline" style="width: 100%; padding: 1rem;">+ Add Payout Method</button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}
</style>
@endsection
