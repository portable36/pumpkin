<?php

namespace App\Services\SKU;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class BarcodeGenerator
{
    public function generate(): string
    {
        $config = $this->getConfiguration();
        
        if (!$config['auto_generate']) {
            return '';
        }

        $parts = [];

        if ($config['prefix']) {
            $parts[] = $config['prefix'];
        }

        $nextNumber = $this->getNextNumber();
        $barcode = ($config['prefix'] ?? '') . str_pad($nextNumber, $config['length'], '0', STR_PAD_LEFT);

        // Generate check digit for EAN13 if needed
        if ($config['type'] === 'EAN13') {
            $barcode = $this->addEAN13CheckDigit($barcode);
        }

        return $barcode;
    }

    protected function addEAN13CheckDigit(string $barcode): string
    {
        if (strlen($barcode) > 12) {
            $barcode = substr($barcode, 0, 12);
        }
        
        $barcode = str_pad($barcode, 12, '0', STR_PAD_LEFT);
        
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $multiplier = ($i % 2 === 0) ? 1 : 3;
            $sum += (int)$barcode[$i] * $multiplier;
        }
        
        $checkDigit = (10 - ($sum % 10)) % 10;
        
        return $barcode . $checkDigit;
    }

    protected function getConfiguration(): array
    {
        return [
            'type' => config('ecommerce.barcode.type', 'CODE128'),
            'prefix' => config('ecommerce.barcode.prefix'),
            'length' => config('ecommerce.barcode.length', 12),
            'auto_generate' => config('ecommerce.barcode.auto_generate', true),
        ];
    }

    protected function getNextNumber(): int
    {
        return DB::transaction(function () {
            $config = DB::table('barcode_configurations')->lockForUpdate()->first();
            
            if (!$config) {
                DB::table('barcode_configurations')->insert([
                    'next_number' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return 1;
            }

            $nextNumber = $config->next_number;
            DB::table('barcode_configurations')
                ->where('id', $config->id)
                ->update(['next_number' => $nextNumber + 1]);

            return $nextNumber;
        });
    }

    public function isValid(string $barcode): bool
    {
        return Product::where('barcode', $barcode)->doesntExist();
    }
}
