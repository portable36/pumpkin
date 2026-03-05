<?php

namespace Modules\Delivery\Entities;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Traits\ModelTrait;
use App\Traits\ModelTraits\hasFiles;
use App\Traits\ModelTraits\Metable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\MediaManager\Http\Models\ObjectFile;

class DeliveryMan extends Model
{
    use hasFiles;
    use Metable;
    use ModelTrait;

    protected $table = 'delivery_mans';

    protected $fillable = ['user_id', 'unique_id', 'license_status', 'is_active'];

    protected $metaTable = 'delivery_mans_meta';

    protected $metaDataFilesArray = ['driving_license_photo'];

    /**
     * Relation with user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the driving license photo meta data.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function getLicenseAttribute()
    {
        return $this->metas()->where('key', 'driving_license_photo')->first();
    }

    /**
     * Get orders assigned to this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignedOrders()
    {
        return $this->hasMany(DeliveryManOrder::class, 'delivery_man_id');
    }

    /**
     * The delivery man that belong to the order.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'delivery_man_orders', 'delivery_man_id', 'order_id');
    }

    /**
     * Create a delivery man instance
     *
     * @return bool
     */
    public function store(array $data)
    {
        $model = parent::create($data['essentialData']);

        if ($model) {
            if (! empty($data['metaData'])) {
                $model->setMeta($data['metaData']);
                $model->save();

                foreach ($data['metaData'] as $key => $value) {
                    if (! in_array($key, $this->metaDataFilesArray)) {
                        continue;
                    }

                    request()->merge(['file_id' => [$value]]);
                    $deliveryManMeta = $model->metas()->where('key', $key)->first();
                    $deliveryManMeta->updateFiles();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Update delivery man information
     *
     * @return bool
     */
    public function updateCarrier(array $data)
    {
        $is_updated = $this->update($data['essentialData']);

        if (! $is_updated) {

            return false;
        }

        if (! empty($data['metaData'])) {
            $this->setMeta($data['metaData']);
            $this->save();

            foreach ($data['metaData'] as $key => $value) {
                if (! in_array($key, $this->metaDataFilesArray)) {
                    continue;
                }

                request()->merge(['file_id' => [$value]]);
                $deliveryManMeta = $this->metas()->where('key', $key)->first();

                if (is_null($value) && in_array($key, $this->metaDataFilesArray)) {
                    $deliveryManMeta->deleteFromMediaManager();
                }

                $deliveryManMeta->updateFiles();
            }
        }

        return true;
    }

    /**
     * Clear related model data
     *
     * @return void
     */
    public static function clearFootprints(int $id)
    {
        $customerRoleId = Role::where('name', 'customer')->first()?->id;
        static::find($id)?->user?->roles()?->sync([$customerRoleId]);

        $metaIds = DeliveryManMeta::where('owner_id', $id)->pluck('id')->toArray();
        ObjectFile::where('object_type', '=', 'delivery_mans_meta')->whereIn('object_id', $metaIds)->delete();
        DeliveryManMeta::where('owner_id', $id)->delete();
    }

    /**
     * Get unique id for delivery man
     *
     * @return string
     */
    public static function getUniqueCarrierId()
    {
        $lastEntry = DeliveryMan::latest('id')->first();

        if (isset($lastEntry)) {
            $newId = strval(intval($lastEntry->id) + 1);
        } else {
            $newId = '1';
        }

        return 'DM' . now()->format('Y') . $newId;
    }
}
