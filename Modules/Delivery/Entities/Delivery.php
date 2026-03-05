<?php

namespace Modules\Delivery\Entities;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $table = 'deliveries';

    protected $fillable = [
        'delivery_man_id',
        'order_id',
        'date',
        'collected_amount',
        'remark',
    ];
}
