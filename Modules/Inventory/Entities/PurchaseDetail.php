<?php

namespace Modules\Inventory\Entities;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * Foreign key with purchase model
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

    /**
     * Increment receive
     */
    public function incrementReceive($value = 0): void
    {
        $this->increment('quantity_receive', $value);
    }

    /**
     * Increment reject
     */
    public function incrementReject($value = 0): void
    {
        $this->increment('quantity_reject', $value);
    }

    /**
     * Foreign key with product model
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * get product display name
     */
    public function getProductDisplayNameAttribute()
    {
        if ($this->product) {
            return $this->product->trashed()
                ? "{$this->product_name} (" . __('deleted') . ')'
                : $this->product_name;
        }

        return "{$this->product_name} (deleted)";
    }

    /**
     * store
     *
     * @return bool
     */
    public static function store($data = [])
    {
        if (parent::insert($data)) {
            return true;
        }

        return false;
    }

    /**
     * update purchase detail
     *
     * @return bool
     */
    public static function updatePurchaseDetail($request, $id)
    {
        $result = parent::where('id', $id);

        if ($result->exists()) {
            $result->update($request);

            return true;
        }

        return false;
    }

    /**
     * remove
     *
     * @return array
     */
    public static function remove($id = null)
    {
        $data = ['type' => 'fail', 'message' => __('Something went wrong, please try again.')];
        $result = parent::find($id);

        if (! empty($result)) {
            $result->delete();
            $data = ['type' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Purchase Detail')])];
        }

        return $data;
    }
}
