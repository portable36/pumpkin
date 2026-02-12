<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Console\Command;

class ProcessVendorPayouts extends Command
{
    protected $signature = 'vendors:process-payouts {--vendor_id= : Process payout for specific vendor}';
    protected $description = 'Process pending vendor payouts';

    public function handle(VendorService $vendorService): int
    {
        $query = Vendor::where('is_active', true);

        if ($this->option('vendor_id')) {
            $query->where('id', $this->option('vendor_id'));
        }

        foreach ($query->get() as $vendor) {
            $this->info("Processing payout for vendor: {$vendor->shop_name}");
            $vendorService->processPayout($vendor, 0);
        }

        return 0;
    }
}
