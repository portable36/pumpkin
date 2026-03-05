<?php

namespace Modules\AdvanceReport\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Modules\AdvanceReport\Services\ReportConfigService;
use Modules\AdvanceReport\Services\ReportDataService;
use Modules\AdvanceReport\Services\Export\ReportExportService;
use Modules\AdvanceReport\Services\Export\ExportFormatterFactory;

class AdvanceReportController extends Controller
{
    protected $reportDataService;

    protected $exportService;

    protected $formatterFactory;

    public function __construct(ReportDataService $reportDataService, ReportExportService $exportService, ExportFormatterFactory $formatterFactory)
    {
        $this->reportDataService = $reportDataService;
        $this->exportService = $exportService;
        $this->formatterFactory = $formatterFactory;
    }

    /**
     * Display a listing of all reports.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $reports = ReportConfigService::getActiveReports();
        $allReportsFlat = ReportConfigService::getAllReportsFlat();

        // Get filter parameters
        $search = $request->get('search', '');
        $categories = $request->get('categories', []);

        // Handle single category parameter for backward compatibility
        if ($request->has('category') && ! $request->has('categories')) {
            $categories = [$request->get('category')];
        }

        // Ensure categories is an array
        if (! is_array($categories)) {
            $categories = $categories ? [$categories] : [];
        }

        // Filter reports if search or categories are provided
        if ($search || ! empty($categories)) {
            $filteredReports = [];

            foreach ($reports as $catKey => $categoryData) {
                // If categories filter is set and current category is not in the selected categories, skip
                if (! empty($categories) && ! in_array($categoryData['slug'], $categories)) {
                    continue;
                }

                $categoryReports = $categoryData['reports'];

                if ($search) {
                    $categoryReports = array_filter($categoryReports, function ($report) use ($search) {
                        return stripos($report['name'], $search) !== false ||
                               stripos($report['description'], $search) !== false;
                    });
                }

                if (! empty($categoryReports)) {
                    $filteredReports[$catKey] = $categoryData;
                    $filteredReports[$catKey]['reports'] = array_values($categoryReports);
                }
            }

            $reports = $filteredReports;
        }

        return view('advancereport::index', compact('reports', 'allReportsFlat', 'search', 'categories'));
    }

    /**
     * Display a specific report.
     *
     * @param  string  $slug
     * @return Renderable
     */
    public function show(Request $request, $slug)
    {
        $report = ReportConfigService::getReportBySlug($slug);

        if (! $report) {
            abort(404, 'Report not found');
        }

        // Get date range from request
        $dateRange = $this->reportDataService->normalizeDateRange(
            $request->get('from'),
            $request->get('to')
        );
        $fromDate = $dateRange['from'];
        $toDate = $dateRange['to'];
        $vendorId = $request->get('vendor_id');

        // Get pagination parameters for product reports
        $perPage = $request->get('per_page', 25);
        $page = $request->get('page', 1);

        // Get search parameter
        $search = $request->get('search', '');

        // Get sorting parameters - set default based on report type
        // Default sort columns for reports that use order_date
        $orderDateReports = ['total-sales-by-order', 'payments-by-order'];
        $defaultSortColumn = in_array($slug, $orderDateReports) ? 'order_date' : 'total_sales';
        $sortColumn = $request->get('sort_column', $defaultSortColumn);
        $sortDirection = $request->get('sort_direction', 'desc');

        // Get payment status filter (for payments-by-order report)
        $paymentStatus = $request->get('payment_status');

        // Get order status filter (for total-sales-by-order report)
        $orderStatus = $request->get('order_status');

        // Get channel filter (for total-sales-by-order report)
        $channel = $request->get('channel');

        // Get all vendors for dropdown
        $vendors = Vendor::getAll()->where('status', 'Active')->sortBy('name');

        // Generate report data using service
        $data = $this->reportDataService->generateReportData($slug, $fromDate, $toDate, $vendorId, $perPage, $page, $search, $sortColumn, $sortDirection, $paymentStatus, $orderStatus, $channel);

        // If this is an AJAX request, return only the report data partial
        if ($request->ajax()) {
            return view('advancereport::partials.report-data', compact('report', 'fromDate', 'toDate', 'data'))->render();
        }

        return view('advancereport::show', compact('report', 'fromDate', 'toDate', 'vendorId', 'vendors', 'data', 'search', 'paymentStatus', 'orderStatus', 'channel'));
    }

    /**
     * Export report data to CSV
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $slug)
    {
        $report = ReportConfigService::getReportBySlug($slug);

        if (! $report) {
            abort(404, 'Report not found');
        }

        // Check if export is available for this report
        if (! $this->formatterFactory->hasExport($slug)) {
            abort(404, __('Export not available for this report'));
        }

        // Get date range from request
        $dateRange = $this->reportDataService->normalizeDateRange(
            $request->get('from'),
            $request->get('to')
        );
        $fromDate = $dateRange['from'];
        $toDate = $dateRange['to'];
        $vendorId = $request->get('vendor_id');
        $search = $request->get('search', '');
        $paymentStatus = $request->get('payment_status');
        $orderStatus = $request->get('order_status');
        $channel = $request->get('channel');

        // Use export service to generate CSV
        return $this->exportService->exportToCsv(
            $slug,
            $fromDate,
            $toDate,
            $vendorId,
            $search,
            $paymentStatus,
            $orderStatus,
            $channel
        );
    }
}
