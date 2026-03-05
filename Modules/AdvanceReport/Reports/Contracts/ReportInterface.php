<?php

namespace Modules\AdvanceReport\Reports\Contracts;

interface ReportInterface
{
    /**
     * Generate report data
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @return array
     */
    public function generate($fromDate, $toDate, $vendorId = null);
}
