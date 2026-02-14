<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\VendorPayout;
use App\Models\VendorLedger;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Comprehensive Vendor Payout Service
 * Handles calculation, scheduling, and processing of vendor payouts
 */
class PayoutService
{
    /**
     * Calculate pending commission for a vendor
     */
    public function calculatePendingCommission(Vendor $vendor): float
    {
        $paidOrders = Order::where('vendor_id', $vendor->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $commissionRate = $vendor->commission_rate ?? Setting::get('commission.default_rate', 10);
        $commission = ($paidOrders * $commissionRate) / 100;

        return max(0, round($commission, 2));
    }

    /**
     * Calculate net payout (sales minus commission)
     */
    public function calculateNetPayout(Vendor $vendor, ?Carbon $period = null): float
    {
        $query = Order::where('vendor_id', $vendor->id)
            ->where('payment_status', 'paid');

        if ($period) {
            $query->whereBetween('created_at', [
                $period->startOfMonth(),
                $period->endOfMonth(),
            ]);
        }

        $totalSales = $query->sum('total_amount');
        $commissionRate = $vendor->commission_rate ?? Setting::get('commission.default_rate', 10);
        $commission = ($totalSales * $commissionRate) / 100;
        $netPayout = $totalSales - $commission;

        return max(0, round($netPayout, 2));
    }

    /**
     * Check if vendor meets payout eligibility
     */
    public function canProcessPayout(Vendor $vendor): bool
    {
        if (!$vendor->is_active || $vendor->is_verified === false) {
            return false;
        }

        $minPayout = Setting::get('commission.min_payout', 500);
        $pendingBalance = $this->calculatePendingBalance($vendor);

        return $pendingBalance >= $minPayout;
    }

    /**
     * Calculate pending balance from ledger
     */
    public function calculatePendingBalance(Vendor $vendor): float
    {
        $balance = VendorLedger::where('vendor_id', $vendor->id)
            ->where('type', 'credit')
            ->sum('amount');

        $deducted = VendorLedger::where('vendor_id', $vendor->id)
            ->where('type', 'debit')
            ->sum('amount');

        return max(0, round($balance - $deducted, 2));
    }

    /**
     * Record a credit to vendor ledger (from order payment)
     */
    public function recordSaleCredit(Vendor $vendor, Order $order): void
    {
        $commissionRate = $vendor->commission_rate ?? Setting::get('commission.default_rate', 10);
        $commission = ($order->total_amount * $commissionRate) / 100;
        $credit = $order->total_amount - $commission;

        $runningBalance = $this->getRunningBalance($vendor);

        VendorLedger::create([
            'vendor_id' => $vendor->id,
            'type' => 'credit',
            'amount' => round($credit, 2),
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'description' => "Sale from Order #{$order->order_number}",
            'running_balance' => $runningBalance + $credit,
        ]);

        // Record commission deduction
        VendorLedger::create([
            'vendor_id' => $vendor->id,
            'type' => 'debit',
            'amount' => round($commission, 2),
            'reference_type' => 'commission',
            'reference_id' => $order->id,
            'description' => "Platform commission ({$commissionRate}%) for Order #{$order->order_number}",
            'running_balance' => $runningBalance + $credit - $commission,
        ]);
    }

    /**
     * Get running balance for a vendor
     */
    protected function getRunningBalance(Vendor $vendor): float
    {
        return VendorLedger::where('vendor_id', $vendor->id)
            ->latest('id')
            ->first()?->running_balance ?? 0;
    }

    /**
     * Create payout record
     */
    public function createPayout(Vendor $vendor, float $amount, string $method = null): VendorPayout
    {
        $method = $method ?? Setting::get('commission.payout_method', 'bank');

        $payout = VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => round($amount, 2),
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'status' => 'pending',
            'requested_at' => now(),
            'notes' => "Payout via {$method}",
        ]);

        // Record payout in ledger
        $runningBalance = $this->getRunningBalance($vendor);
        VendorLedger::create([
            'vendor_id' => $vendor->id,
            'type' => 'debit',
            'amount' => round($amount, 2),
            'reference_type' => 'payout',
            'reference_id' => $payout->id,
            'description' => "Payout #{$payout->id}",
            'running_balance' => $runningBalance - $amount,
        ]);

        Log::info("Payout created", [
            'vendor_id' => $vendor->id,
            'payout_id' => $payout->id,
            'amount' => $amount,
            'method' => $method,
        ]);

        return $payout;
    }

    /**
     * Process all eligible vendor payouts
     */
    public function processAllEligiblePayouts(): void
    {
        if (!Setting::get('commission.auto_payout_enabled', false)) {
            Log::info("Auto-payout disabled, skipping");
            return;
        }

        $payoutDay = Setting::get('commission.auto_payout_day', 1);
        if (now()->day !== $payoutDay) {
            Log::info("Not payout day (configured: {$payoutDay}, today: " . now()->day . ")");
            return;
        }

        $vendors = Vendor::where('is_active', true)
            ->where('is_verified', true)
            ->get();

        $processedCount = 0;
        $totalAmount = 0;

        foreach ($vendors as $vendor) {
            if ($this->canProcessPayout($vendor)) {
                $balance = $this->calculatePendingBalance($vendor);
                $this->createPayout($vendor, $balance);
                $processedCount++;
                $totalAmount += $balance;
            }
        }

        Log::info("Auto-payout process completed", [
            'vendors_processed' => $processedCount,
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Mark payout as processed
     */
    public function markAsProcessed(VendorPayout $payout, string $transactionId = null): VendorPayout
    {
        $payout->update([
            'status' => 'processed',
            'transaction_id' => $transactionId,
            'processed_at' => now(),
        ]);

        Log::info("Payout marked as processed", [
            'payout_id' => $payout->id,
            'transaction_id' => $transactionId,
        ]);

        return $payout;
    }

    /**
     * Mark payout as failed
     */
    public function markAsFailed(VendorPayout $payout, string $reason = null): VendorPayout
    {
        $payout->update([
            'status' => 'failed',
            'notes' => $reason ?? 'Payment processing failed',
        ]);

        Log::warning("Payout failed", [
            'payout_id' => $payout->id,
            'reason' => $reason,
        ]);

        return $payout;
    }

    /**
     * Get vendor payout history
     */
    public function getPayoutHistory(Vendor $vendor, int $limit = 10): Collection
    {
        return VendorPayout::where('vendor_id', $vendor->id)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get vendor ledger entries
     */
    public function getLedgerEntries(Vendor $vendor, int $limit = 50): Collection
    {
        return VendorLedger::where('vendor_id', $vendor->id)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get payout statistics
     */
    public function getPayoutStats(Vendor $vendor): array
    {
        $totalPaid = VendorPayout::where('vendor_id', $vendor->id)
            ->where('status', 'processed')
            ->sum('amount');

        $pendingPayouts = VendorPayout::where('vendor_id', $vendor->id)
            ->where('status', 'pending')
            ->sum('amount');

        $totalEarnings = Order::where('vendor_id', $vendor->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $commissionRate = $vendor->commission_rate ?? Setting::get('commission.default_rate', 10);
        $totalCommission = ($totalEarnings * $commissionRate) / 100;
        $netEarnings = $totalEarnings - $totalCommission;

        return [
            'total_earnings' => round($totalEarnings, 2),
            'total_commission' => round($totalCommission, 2),
            'net_earnings' => round($netEarnings, 2),
            'total_paid' => round($totalPaid, 2),
            'pending_payout' => round($pendingPayouts, 2),
            'available_balance' => round($netEarnings - $totalPaid, 2),
        ];
    }
}
