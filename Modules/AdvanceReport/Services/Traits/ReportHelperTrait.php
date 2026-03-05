<?php

namespace Modules\AdvanceReport\Services\Traits;

use Carbon\Carbon;

trait ReportHelperTrait
{
    /**
     * Validate and normalize date range
     *
     * @param  string|null  $fromDate
     * @param  string|null  $toDate
     * @return array ['from' => string, 'to' => string]
     */
    public function normalizeDateRange($fromDate = null, $toDate = null)
    {
        // Set default to last 30 days if not provided
        if (empty($fromDate) || empty($toDate)) {
            $toDate = date('Y-m-d');
            $fromDate = date('Y-m-d', strtotime('-30 days'));
        } else {
            // Validate and format dates
            $fromDate = DbDateFormat($fromDate);
            $toDate = DbDateFormat($toDate);

            // Ensure from_date <= to_date
            if ($fromDate > $toDate) {
                $temp = $fromDate;
                $fromDate = $toDate;
                $toDate = $temp;
            }
        }

        return [
            'from' => $fromDate,
            'to' => $toDate,
        ];
    }

    /**
     * Calculate percentage change between two values
     *
     * @param  float  $current
     * @param  float  $previous
     * @return array ['value' => float, 'direction' => string]
     */
    public function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            if ($current > 0) {
                return ['value' => 100, 'direction' => 'up'];
            }

            return ['value' => 0, 'direction' => 'neutral'];
        }

        $change = (($current - $previous) / $previous) * 100;

        if ($change > 0) {
            return ['value' => abs($change), 'direction' => 'up'];
        } elseif ($change < 0) {
            return ['value' => abs($change), 'direction' => 'down'];
        }

        return ['value' => 0, 'direction' => 'neutral'];
    }

    /**
     * Format period label for display
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return string
     */
    public function formatPeriodLabel($fromDate, $toDate)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        // Check if it's the same month
        if ($from->format('Y-m') === $to->format('Y-m')) {
            return $from->format('M d') . ' - ' . $to->format('d, Y');
        }

        return $from->format('M d') . ' - ' . $to->format('M d, Y');
    }

    /**
     * Determine grouping type based on date range
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return string 'day', 'month', or 'year'
     */
    public function determineGroupBy($fromDate, $toDate)
    {
        $dateDiffInDays = Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate));
        $dateDiffInMonths = Carbon::parse($fromDate)->diffInMonths(Carbon::parse($toDate));

        if ($dateDiffInDays <= 90) {
            return 'day';
        } elseif ($dateDiffInMonths <= 36) {
            return 'month';
        } else {
            return 'year';
        }
    }

    /**
     * Calculate previous period dates based on current period
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return array ['from' => string, 'to' => string]
     */
    public function getPreviousPeriodDates($fromDate, $toDate)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        // Calculate the number of days in the current period
        $daysDifference = $from->diffInDays($to);

        // Calculate previous period (same duration, shifted back)
        $previousTo = $from->copy()->subDay();
        $previousFrom = $previousTo->copy()->subDays($daysDifference);

        return [
            'from' => $previousFrom->format('Y-m-d'),
            'to' => $previousTo->format('Y-m-d'),
        ];
    }
}
