<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\VendorLedger;
use App\Models\VendorPayout;
use App\Models\Order;

class VendorService
{
    /**
     * Get vendor dashboard stats
     */
    public function getVendorStats(Vendor $vendor): array
    {
        $orders = Order::where('vendor_id', $vendor->id)
            ->where('payment_status', 'paid');

        $thisMonth = $orders->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $totalEarnings = $orders->sum('total_amount');
        $commission = ($totalEarnings * $vendor->commission_rate) / 100;

        return [
            'total_products' => $vendor->products()->count(),
            'total_orders' => Order::where('vendor_id', $vendor->id)->count(),
            'total_earnings' => round($totalEarnings, 2),
            'commission_deducted' => round($commission, 2),
            'net_earnings' => round($totalEarnings - $commission, 2),
            'this_month_earnings' => round($thisMonth, 2),
            'pending_payouts' => VendorPayout::where('vendor_id', $vendor->id)
                ->where('status', 'pending')
                ->sum('amount'),
            'followers' => $vendor->followers_count,
            'rating' => round($vendor->rating, 2),
        ];
    }

    /**
     * Record vendor earnings
     */
    public function recordEarning(Vendor $vendor, float $amount, string $referenceType, $referenceId): VendorLedger
    {
        $runningBalance = VendorLedger::where('vendor_id', $vendor->id)
            ->latest()
            ->first()?->running_balance ?? 0;

        $newBalance = $runningBalance + $amount;

        return VendorLedger::create([
            'vendor_id' => $vendor->id,
            'type' => 'credit',
            'amount' => $amount,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => "Order {$referenceId} earnings",
            'running_balance' => $newBalance,
        ]);
    }

    /**
     * Process vendor payout
     */
    public function processPayout(Vendor $vendor, float $amount, string $description = null): VendorPayout
    {
        // Deduct commission
        $commissionRate = $vendor->commission_rate / 100;
        $commission = $amount * $commissionRate;
        $payoutAmount = $amount - $commission;

        // Record ledger entry
        $runningBalance = VendorLedger::where('vendor_id', $vendor->id)
            ->latest()
            ->first()?->running_balance ?? 0;

        VendorLedger::create([
            'vendor_id' => $vendor->id,
            'type' => 'debit',
            'amount' => $commissionAmount = $commission,
            'reference_type' => 'commission',
            'reference_id' => null,
            'description' => 'Platform commission',
            'running_balance' => $runningBalance - $commission,
        ]);

        return VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => round($payoutAmount, 2),
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'status' => 'pending',
            'requested_at' => now(),
            'notes' => $description,
        ]);
    }

    /**
     * Get vendor ledger
     */
    public function getVendorLedger(Vendor $vendor, int $limit = 50): \Illuminate\Pagination\Paginator
    {
        return VendorLedger::where('vendor_id', $vendor->id)
            ->latest()
            ->paginate($limit);
    }

    /**
     * Update vendor rating
     */
    public function updateVendorRating(Vendor $vendor): void
    {
        $reviews = $vendor->reviews()->approved()->get();

        if ($reviews->isEmpty()) {
            return;
        }

        $averageRating = $reviews->avg('rating');
        $reviewsCount = $reviews->count();

        $vendor->update([
            'rating' => round($averageRating, 2),
            'reviews_count' => $reviewsCount,
        ]);
    }
}
