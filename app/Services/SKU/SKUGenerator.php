<?php

namespace App\Services\SKU;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SKUGenerator
{
    public function generate(?int $categoryId = null, ?int $vendorId = null, ?string $productName = null): string
    {
        $config = $this->getConfiguration();
        
        if (!$config['auto_generate']) {
            return '';
        }

        $parts = [];

        if ($config['prefix']) {
            $parts[] = $config['prefix'];
        }

        if ($config['include_category_code'] && $categoryId) {
            $parts[] = $this->getCategoryCode($categoryId);
        }

        if ($config['include_vendor_code'] && $vendorId) {
            $parts[] = $this->getVendorCode($vendorId);
        }

        $nextNumber = $this->getNextNumber();
        $parts[] = str_pad($nextNumber, $config['length'], '0', STR_PAD_LEFT);

        if ($config['suffix']) {
            $parts[] = $config['suffix'];
        }

        return implode('-', array_filter($parts));
    }

    public function generateVariantSKU(string $baseSKU, array $attributes): string
    {
        $suffix = [];
        
        foreach ($attributes as $attributeValue) {
            $suffix[] = strtoupper(substr($attributeValue, 0, 3));
        }
        
        return $baseSKU . '-' . implode('', $suffix);
    }

    protected function getConfiguration(): array
    {
        return [
            'prefix' => config('ecommerce.sku.prefix', 'PRD'),
            'suffix' => config('ecommerce.sku.suffix'),
            'length' => config('ecommerce.sku.length', 8),
            'auto_generate' => config('ecommerce.sku.auto_generate', true),
            'include_category_code' => config('ecommerce.sku.include_category_code', false),
            'include_vendor_code' => config('ecommerce.sku.include_vendor_code', false),
        ];
    }

    protected function getCategoryCode(int $categoryId): string
    {
        return 'CAT' . str_pad($categoryId, 3, '0', STR_PAD_LEFT);
    }

    protected function getVendorCode(int $vendorId): string
    {
        return 'VEN' . str_pad($vendorId, 3, '0', STR_PAD_LEFT);
    }

    protected function getNextNumber(): int
    {
        return DB::transaction(function () {
            $config = DB::table('sku_configurations')->lockForUpdate()->first();
            
            if (!$config) {
                DB::table('sku_configurations')->insert([
                    'next_number' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return 1;
            }

            $nextNumber = $config->next_number;
            DB::table('sku_configurations')
                ->where('id', $config->id)
                ->update(['next_number' => $nextNumber + 1]);

            return $nextNumber;
        });
    }

    public function isValid(string $sku): bool
    {
        return Product::where('sku', $sku)->doesntExist();
    }
}
