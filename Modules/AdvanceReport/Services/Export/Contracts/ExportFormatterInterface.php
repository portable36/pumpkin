<?php

namespace Modules\AdvanceReport\Services\Export\Contracts;

interface ExportFormatterInterface
{
    /**
     * Format report data for CSV export
     *
     * @param  array  $data  Report data from generate() method
     * @return array Array of rows, each row is an array of values
     */
    public function format(array $data, string $fromDate, string $toDate): array;

    /**
     * Get CSV headers for this report
     */
    public function getHeaders(): array;
}
