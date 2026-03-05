<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Soumik Datta <[soumik.techvill@gmail.com]>
 *
 * @created 07-02-2023
 */

namespace Modules\Delivery\Filters;

use App\Filters\Filter;

class DeliveryManFilter extends Filter
{
    /**
     * filter by is_active query string
     *
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function isActive($status)
    {
        return $this->query->where('is_active', $status);
    }

    /**
     * filter by license_status query string
     *
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function licenseStatus($status)
    {
        return $this->query->where('license_status', $status);
    }

    /**
     * filter by search query string
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function search($value)
    {
        $value = xss_clean(trim($value['value']));

        if (! empty($value)) {
            $this->query->where('unique_id', 'LIKE', '%' . $value . '%')
                ->orWhereHas('user', function ($query) use ($value) {
                    $query->where('name', 'LIKE', '%' . $value . '%');
                });
        }

        return $this->query;
    }
}
