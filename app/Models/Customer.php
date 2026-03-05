<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class Customer extends Authenticatable
{
    use HasFactory;
    use ModelTrait;
    use Notifiable;

    protected $table = 'customers';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'vendor_id', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Store a new customer.
     *
     * @return int
     */
    /**
     * Store a new customer and return the ID.
     *
     * @return int|null
     */
    public function storeCustomer(array $data)
    {
        $fillableFields = [
            'name',
            'email',
            'phone',
            'password',
            'vendor_id',
            'status',
            'birthday',
            'gender',
        ];

        foreach ($fillableFields as $field) {
            // Only set the attribute if it exists in $data, else set null for optional fields
            $this->$field = array_key_exists($field, $data) ? $data[$field] : null;
        }

        $this->save();

        return $this->id ?? null;
    }

    /**
     * Update a customer.
     *
     * @param  array  $data
     * @param  Customer  $customer
     * @return int
     */
    public function updateCustomer($data, $customer)
    {
        $customer->name = $data['name'];
        $customer->email = $data['email'];
        $customer->phone = $data['phone'];
        $customer->vendor_id = $data['vendor_id'] ?? $customer->vendor_id;
        $customer->save();

        return $customer->id;
    }

    /**
     * Get the route notifications for the mail channel.
     *
     * @return bool
     */
    public function routeNotificationForMail(Notification $notification)
    {
        return false;
    }
}
