<?php

namespace App\Services\Inventory;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function reserveStock(
        int $warehouseId,
        int $productId,
        int $quantity,
        ?int $variantId = null,
        string $referenceType = 'order',
        ?int $referenceId = null
    ): bool {
        return DB::transaction(function () use ($warehouseId, $productId, $quantity, $variantId, $referenceType, $referenceId) {
            $inventory = Inventory::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if (!$inventory || $inventory->available_quantity < $quantity) {
                throw new \Exception('Insufficient stock available');
            }

            $beforeQuantity = $inventory->quantity;
            $inventory->increment('reserved_quantity', $quantity);

            $this->logTransaction([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'type' => 'stock_out',
                'quantity' => -$quantity,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $inventory->quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => 'Stock reserved for ' . $referenceType,
                'created_by' => auth()->id(),
            ]);

            // Check if low stock alert needed
            $this->checkLowStock($inventory);

            return true;
        });
    }

    public function releaseStock(
        int $warehouseId,
        int $productId,
        int $quantity,
        ?int $variantId = null,
        string $reason = 'Reservation released'
    ): bool {
        return DB::transaction(function () use ($warehouseId, $productId, $quantity, $variantId, $reason) {
            $inventory = Inventory::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                return false;
            }

            $beforeQuantity = $inventory->quantity;
            $inventory->decrement('reserved_quantity', min($quantity, $inventory->reserved_quantity));

            $this->logTransaction([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'type' => 'adjustment',
                'quantity' => 0,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $inventory->quantity,
                'notes' => $reason,
                'created_by' => auth()->id(),
            ]);

            return true;
        });
    }

    public function deductStock(
        int $warehouseId,
        int $productId,
        int $quantity,
        ?int $variantId = null,
        string $referenceType = 'order',
        ?int $referenceId = null
    ): bool {
        return DB::transaction(function () use ($warehouseId, $productId, $quantity, $variantId, $referenceType, $referenceId) {
            $inventory = Inventory::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                throw new \Exception('Inventory record not found');
            }

            $beforeQuantity = $inventory->quantity;
            
            // Deduct from reserved if available, otherwise from regular stock
            if ($inventory->reserved_quantity >= $quantity) {
                $inventory->decrement('reserved_quantity', $quantity);
            } else {
                $fromReserved = $inventory->reserved_quantity;
                $fromRegular = $quantity - $fromReserved;
                
                $inventory->reserved_quantity = 0;
                if ($inventory->quantity - $fromReserved < $fromRegular) {
                    throw new \Exception('Insufficient stock');
                }
            }
            
            $inventory->decrement('quantity', $quantity);

            $this->logTransaction([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'type' => 'stock_out',
                'quantity' => -$quantity,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $inventory->quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => 'Stock deducted for ' . $referenceType,
                'created_by' => auth()->id(),
            ]);

            // Update product stock
            $this->updateProductStock($productId, $variantId);

            // Check low stock
            $this->checkLowStock($inventory);

            return true;
        });
    }

    public function addStock(
        int $warehouseId,
        int $productId,
        int $quantity,
        ?int $variantId = null,
        string $referenceType = 'purchase',
        ?int $referenceId = null
    ): bool {
        return DB::transaction(function () use ($warehouseId, $productId, $quantity, $variantId, $referenceType, $referenceId) {
            $inventory = Inventory::firstOrCreate(
                [
                    'warehouse_id' => $warehouseId,
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'reorder_level' => 10,
                ]
            );

            $beforeQuantity = $inventory->quantity;
            $inventory->increment('quantity', $quantity);

            $this->logTransaction([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'type' => 'stock_in',
                'quantity' => $quantity,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $inventory->quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => 'Stock added from ' . $referenceType,
                'created_by' => auth()->id(),
            ]);

            // Update product stock
            $this->updateProductStock($productId, $variantId);

            return true;
        });
    }

    public function adjustStock(
        int $warehouseId,
        int $productId,
        int $newQuantity,
        string $reason,
        ?int $variantId = null
    ): bool {
        return DB::transaction(function () use ($warehouseId, $productId, $newQuantity, $reason, $variantId) {
            $inventory = Inventory::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->lockForUpdate()
                ->firstOrFail();

            $beforeQuantity = $inventory->quantity;
            $difference = $newQuantity - $beforeQuantity;
            
            $inventory->update(['quantity' => $newQuantity]);

            $this->logTransaction([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'type' => 'adjustment',
                'quantity' => $difference,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $newQuantity,
                'reference_type' => 'adjustment',
                'notes' => $reason,
                'created_by' => auth()->id(),
            ]);

            // Update product stock
            $this->updateProductStock($productId, $variantId);

            // Check low stock
            $this->checkLowStock($inventory);

            return true;
        });
    }

    protected function updateProductStock(int $productId, ?int $variantId = null): void
    {
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $totalStock = Inventory::where('product_variant_id', $variantId)
                    ->sum('quantity');
                $variant->update(['stock' => $totalStock]);
            }
        } else {
            $product = Product::find($productId);
            if ($product) {
                $totalStock = Inventory::where('product_id', $productId)
                    ->whereNull('product_variant_id')
                    ->sum('quantity');
                $product->update(['stock' => $totalStock]);
            }
        }
    }

    protected function logTransaction(array $data): InventoryTransaction
    {
        return InventoryTransaction::create($data);
    }

    protected function checkLowStock(Inventory $inventory): void
    {
        if ($inventory->available_quantity <= $inventory->reorder_level) {
            LowStockAlert::firstOrCreate(
                [
                    'product_id' => $inventory->product_id,
                    'product_variant_id' => $inventory->product_variant_id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'is_resolved' => false,
                ],
                [
                    'current_stock' => $inventory->available_quantity,
                    'reorder_level' => $inventory->reorder_level,
                ]
            );
        }
    }

    public function getAvailableStock(int $productId, ?int $variantId = null, ?int $warehouseId = null): int
    {
        $query = Inventory::where('product_id', $productId)
            ->where('product_variant_id', $variantId);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->sum(DB::raw('quantity - reserved_quantity'));
    }
}
