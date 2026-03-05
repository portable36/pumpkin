<?php

namespace Modules\AdvanceReport\Services\Export;

use Modules\AdvanceReport\Services\Export\Contracts\ExportFormatterInterface;
use Modules\AdvanceReport\Services\ReportDataService;

class ReportExportService
{
    protected $reportDataService;

    protected $formatterFactory;

    public function __construct(ReportDataService $reportDataService, ExportFormatterFactory $formatterFactory)
    {
        $this->reportDataService = $reportDataService;
        $this->formatterFactory = $formatterFactory;
    }

    /**
     * Export report to CSV
     *
     * @return \Illuminate\Http\Response
     */
    public function exportToCsv(
        string $slug,
        string $fromDate,
        string $toDate,
        ?int $vendorId = null,
        string $search = '',
        ?string $paymentStatus = null,
        ?string $orderStatus = null,
        ?string $channel = null
    ) {
        // Get formatter for this report
        $formatter = $this->formatterFactory->create($slug);

        if (! $formatter instanceof ExportFormatterInterface) {
            abort(404, __('Export not available for this report'));
        }

        // Generate filename
        $filename = $slug . '-' . date('Y-m-d-His') . '.csv';

        // Set headers for CSV download
        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Create CSV content with incremental pagination
        $callback = function () use ($slug, $fromDate, $toDate, $vendorId, $search, $paymentStatus, $orderStatus, $channel, $formatter) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8
            fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $perPage = 1000; // Reasonable page size for export
            $page = 1;
            $headerRowCount = 0; // Track how many header/summary rows to skip on subsequent pages

            do {
                $pageData = $this->reportDataService->generateReportData(
                    $slug,
                    $fromDate,
                    $toDate,
                    $vendorId,
                    $perPage,
                    $page,
                    $search,
                    null, // sortColumn
                    null, // sortDirection
                    $paymentStatus,
                    $orderStatus,
                    $channel
                );

                // Check if report has error message
                if (isset($pageData['message'])) {
                    if ($page === 1) {
                        fclose($file);
                        abort(404, $pageData['message']);
                    }
                    break;
                }

                // Format this page's data
                $formattedData = $formatter->format($pageData, $fromDate, $toDate);

                // Write rows to stream
                foreach ($formattedData as $index => $row) {
                    // On first page, write all rows (headers, summary, data)
                    // On subsequent pages, skip header/summary rows (first few rows)
                    if ($page === 1 || $index >= $headerRowCount) {
                        fputcsv($file, $row);
                    }
                }

                // On first page, determine how many header/summary rows there are
                if ($page === 1) {
                    $headerRowCount = $this->countHeaderRows($formattedData);
                }

                // Check if there are more pages
                $hasMorePages = false;
                if (isset($pageData['paginator'])) {
                    $hasMorePages = $page < $pageData['paginator']->lastPage();
                } elseif (isset($pageData['comparisonData'])) {
                    // For time series reports, check if we got a full page
                    $hasMorePages = count($pageData['comparisonData']) === $perPage;
                } else {
                    // For other report types, check if data exists
                    $dataKey = $this->getDataKey($pageData);
                    if ($dataKey && isset($pageData[$dataKey])) {
                        $hasMorePages = count($pageData[$dataKey]) === $perPage;
                    }
                }

                $page++;
            } while ($hasMorePages);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Count header, summary, and separator rows at the beginning
     * These rows should only be written once (on first page)
     *
     * @param  array  $formattedData
     * @return int
     */
    protected function countHeaderRows(array $formattedData): int
    {
        $count = 0;

        foreach ($formattedData as $index => $row) {
            // First row is always headers
            if ($index === 0) {
                $count++;
                continue;
            }

            // Check if this is a summary row (contains "Current Period", "Total", or comparison separator "/")
            $rowString = implode('', $row);
            if (stripos($rowString, __('Current Period')) !== false ||
                stripos($rowString, __('Total')) !== false ||
                (stripos($rowString, '/') !== false && stripos($rowString, __('Previous Period')) !== false)) {
                $count++;
                continue;
            }

            // Empty row after summary is a separator
            if (empty($row) && $index > 0) {
                $count++;
                continue;
            }

            // If we've hit data rows, stop counting
            break;
        }

        return $count;
    }

    /**
     * Get the data key from report data structure
     *
     * @param  array  $data
     * @return string|null
     */
    protected function getDataKey(array $data): ?string
    {
        $possibleKeys = ['customerData', 'productData', 'comparisonData', 'staffData', 'orderData'];
        foreach ($possibleKeys as $key) {
            if (isset($data[$key])) {
                return $key;
            }
        }

        return null;
    }
}
