<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Md Mostafijur Rahman <[mostafijur.techvill@gmail.com]>
 *
 * @created 07-08-2023
 */

namespace Modules\Delivery\Filters;

use App\Filters\Filter;

class CarrierOrderFilter extends Filter
{
    /**
     * filter by order status query string
     *
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function orderStatus($status)
    {
        return $this->query->where('order_status_id', $status);
    }
}
