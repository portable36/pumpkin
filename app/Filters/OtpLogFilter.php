<?php

namespace App\Filters;

class OtpLogFilter extends Filter
{
    /**
     * set the rules of query string
     *
     * @var array
     */
    protected $filterRules = [
        'start_date' => 'nullable',
        'end_date' => 'nullable',
        'type' => 'nullable',
        'channel' => 'nullable',
        'status' => 'nullable',
    ];

    /**
     * Filter by start date
     *
     * @param  string  $startDate
     * @return query builder
     */
    public function startDate($startDate)
    {
        if ($startDate != 'null') {
            return $this->query->whereDate('created_at', '>=', DbDateFormat($startDate));
        }
    }

    /**
     * Filter by end date
     *
     * @param  string  $endDate
     * @return query builder
     */
    public function endDate($endDate)
    {
        if ($endDate != 'null') {
            return $this->query->whereDate('created_at', '<=', DbDateFormat($endDate));
        }
    }

    /**
     * Filter by type
     *
     * @param  string  $type
     * @return query builder
     */
    public function type($type)
    {
        if (!empty($type) && $type !== 'null') {
            return $this->query->where('type', $type);
        }
    }

    /**
     * Filter by channel
     *
     * @param  string  $channel
     * @return query builder
     */
    public function channel($channel)
    {
        if (!empty($channel) && $channel != 'null' && $channel !== '') {
            return $this->query->where('channel', $channel);
        }
    }

    /**
     * Filter by status
     *
     * @param  string  $status
     * @return query builder
     */
    public function status($status)
    {
        if (!empty($status) && $status != 'null' && $status !== '') {
            return $this->query->where('status', $status);
        }
    }

    /**
     * Filter by search query string
     *
     * @param  string  $value
     * @return query builder
     */
    public function search($value)
    {
        $value = xss_clean($value['value']);
        if (! empty($value)) {
            return $this->query->where(function ($query) use ($value) {
                $query->whereLike('email', $value)
                    ->orWhereLike('phone', $value);
                
                // Also search for phone with '+' prefix if value doesn't have it
                if (!str_starts_with($value, '+')) {
                    $query->orWhereLike('phone', '+' . $value);
                }
                // Also search for phone without '+' prefix if value has it
                if (str_starts_with($value, '+')) {
                    $query->orWhereLike('phone', ltrim($value, '+'));
                }
            });
        }
    }
}

