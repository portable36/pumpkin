<?php

namespace Modules\Delivery\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;
use App\Models\OrderDetail;

class DeliveryCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_man_id',
        'order_id',
        'order_details_id',
        'date',
        'amount',
        'approval_time',
        'status',
        'remark',
    ];

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_details_id');
    }

    public function carrier()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }

    /**
     * store
     *
     * @return bool
     */
    public function store($data = [])
    {
        if (parent::insert($data)) {
            return true;
        }

        return false;
    }

    /**
     * Store or Update
     *
     * @param  array  $data
     * @return bool
     */
    public function storeOrUpdate($data = [])
    {
        if (parent::updateOrInsert(['order_id' => $data['order_id'], 'order_details_id' => $data['order_details_id']], $data)) {
            return true;
        }

        return false;
    }

    /**
     * Store or Update
     *
     * @param  array  $data
     * @return bool
     */
    public function storeOrUpdateCategory($data = [])
    {
        if (parent::updateOrInsert(['order_id' => $data['order_id'], 'category_id' => $data['category_id']], $data)) {
            return true;
        }

        return false;
    }

    /**
     * Update Order Commission
     *
     * @param  array  $data
     * @param  null  $id
     * @return bool
     */
    public function updateOrderCommission($data = [], $id = null)
    {
        $result = parent::where('id', $id);
        if ($result->exists()) {
            $result->update($data);

            return true;
        }

        return false;
    }

    /**
     * Update Order Commission By OrderDetail
     *
     * @param  array  $data
     * @param  null  $id
     * @return bool
     */
    public function updateOrderCommissionByOrder($data = [], $id = null)
    {
        $result = parent::where('order_details_id', $id);
        if ($result->exists()) {
            $result->update($data);

            return true;
        }

        return false;
    }
}
