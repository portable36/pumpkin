<?php

namespace App\Filters;

class CustomerFilter extends Filter
{
    public function vendor($value)
    {
        return $this->query->where('vendor_id', $value);
    }

    public function search($value)
    {
        $value = xss_clean($value['value']);

        return $this->query->where(function ($query) use ($value) {
            $query->whereLike('customers.name', $value)
                ->OrWhereLike('customers.email', $value)
                ->OrWhereLike('customers.phone', $value)
                ->orWhereHas('vendor', function ($query) use ($value) {
                    $query->whereLike('name', $value);
                });
        });
    }
}
