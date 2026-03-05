<?php

namespace Modules\Delivery\Entities;

use App\Models\OrderStatus;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryManOrder extends Model
{
    protected $table = 'delivery_man_orders';

    /**
     * Relation with delivery man
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class);
    }

    /**
     * Get the user's most recent order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * get finalOrder status
     */
    public static function getDeliveredOrderStatus(): mixed
    {
        $orderStatus = OrderStatus::getAll()->where('slug', 'delivered')->first();

        if (! empty($orderStatus)) {
            return $orderStatus->id;
        }

        return false;
    }
}
