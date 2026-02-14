<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use App\Services\PayoutService;
use Illuminate\Console\Command;

class ProcessVendorPayouts extends Command
{
    protected $signature = 'vendors:process-payouts {--vendor_id= : Process payout for specific vendor} {--force : Force processing regardless of schedule}';
    protected $description = 'Process pending vendor payouts';

    public function handle(PayoutService $payoutService): int
    {
        if ($this->option('vendor_id')) {
            $vendor = Vendor::find($this->option('vendor_id'));
            if (!$vendor) {
                $this->error("Vendor not found");
                return 1;
            }

            if ($payoutService->canProcessPayout($vendor)) {
                $balance = $payoutService->calculatePendingBalance($vendor);
                $payout = $payoutService->createPayout($vendor, $balance);
                $this->info("✓ Payout created for {$vendor->shop_name}: ৳{$balance}");
            } else {
                $this->warn("Vendor {$vendor->shop_name} is not eligible for payout");
            }

            return 0;
        }

        if ($this->option('force')) {
            $this->info("Processing all eligible vendors (force mode)...");
            $vendors = Vendor::where('is_active', true)->where('is_verified', true)->get();
        } else {
            $this->info("Processing payouts for scheduled day...");
            $payoutService->processAllEligiblePayouts();
            return 0;
        }

        $processed = 0;
        $totalAmount = 0;

        foreach ($vendors as $vendor) {
            if ($payoutService->canProcessPayout($vendor)) {
                $balance = $payoutService->calculatePendingBalance($vendor);
                $payoutService->createPayout($vendor, $balance);
                $this->line("✓ {$vendor->shop_name}: ৳{$balance}");
                $processed++;
                $totalAmount += $balance;
            }
        }

        $this->info("");
        $this->info("Summary:");
        $this->line("  Vendors processed: {$processed}");
        $this->line("  Total amount: ৳{$totalAmount}");

        return 0;
    }
}
