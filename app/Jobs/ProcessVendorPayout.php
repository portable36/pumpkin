<?php

namespace App\Jobs;

use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessVendorPayout implements ShouldQueue
{
    use Queueable;

    public function __construct(public Vendor $vendor) {}

    public function handle(VendorService $vendorService): void
    {
        // Calculate earnings from order payments
        $paidOrders = $vendor->orders()
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        if ($paidOrders > 0) {
            $vendorService->processPayout($vendor, $paidOrders);
        }
    }
}
