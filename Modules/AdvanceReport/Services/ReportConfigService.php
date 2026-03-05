<?php

namespace Modules\AdvanceReport\Services;

class ReportConfigService
{
    /**
     * Get all available reports with their configuration
     *
     * @return array
     */
    public static function getAllReports()
    {
        return [
            'sales' => [
                'category' => 'Sales',
                'slug' => 'sales',
                'enabled' => true,
                'reports' => [
                    [
                        'slug' => 'total-sales-by-vendor',
                        'name' => __('Total sales by vendor'),
                        'description' => __('View total sales grouped by vendor'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'total-sales-over-time',
                        'name' => __('Total sales over time'),
                        'description' => __('View sales trends over time'),
                        'enabled' => true,
                    ],
                    [
                        'slug' => 'sales-by-customer-name',
                        'name' => __('Sales by customer name'),
                        'description' => __('View sales grouped by customer'),
                        'enabled' => true,
                    ],
                    [
                        'slug' => 'total-sales-by-product',
                        'name' => __('Total sales by product'),
                        'description' => __('View sales grouped by product'),
                        'enabled' => true,
                    ],
                    [
                        'slug' => 'total-sales-by-sales-channel',
                        'name' => __('Total sales by sales channel'),
                        'description' => __('View sales by channel (web/pos)'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'average-order-quantity-over-time',
                        'name' => __('Average order quantity over time'),
                        'description' => __('View average order quantity trends'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'net-sales-over-time',
                        'name' => __('Net sales over time'),
                        'description' => __('View net sales after refunds'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'gross-sales-by-sales-channel',
                        'name' => __('Gross sales by sales channel'),
                        'description' => __('View gross sales by channel'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'gross-sales-over-time',
                        'name' => __('Gross sales over time'),
                        'description' => __('View gross sales trends'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'net-sales-by-sales-channel',
                        'name' => __('Net sales by sales channel'),
                        'description' => __('View net sales by channel'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'new-vs-returning-customer-sales',
                        'name' => __('New vs returning customer sales'),
                        'description' => __('Compare sales from new vs returning customers'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'orders-over-time',
                        'name' => __('Orders over time'),
                        'description' => __('View order count trends'),
                        'enabled' => false,
                    ],
                ],
            ],
            'customers' => [
                'category' => 'Customers',
                'slug' => 'customers',
                'enabled' => false,
                'reports' => [
                    [
                        'slug' => 'new-vs-returning-customers',
                        'name' => __('New vs returning customers'),
                        'description' => __('Compare new vs returning customer counts'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'new-customers-over-time',
                        'name' => __('New customers over time'),
                        'description' => __('View new customer acquisition trends'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'returning-customers',
                        'name' => __('Returning customers'),
                        'description' => __('View list of returning customers'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'one-time-customers',
                        'name' => __('One-time customers'),
                        'description' => __('View customers with only one order'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'new-vs-returning-customers-over-time',
                        'name' => __('New vs returning customers over time'),
                        'description' => __('Compare customer types over time'),
                        'enabled' => false,
                    ],
                ],
            ],
            'pos' => [
                'category' => 'POS',
                'slug' => 'pos',
                'enabled' => true,
                'reports' => [
                    [
                        'slug' => 'pos-staff-daily-sales-total',
                        'name' => __('POS staff daily sales total'),
                        'description' => __('View daily sales by POS staff'),
                        'enabled' => true,
                    ],
                    [
                        'slug' => 'pos-staff-sales-total',
                        'name' => __('POS staff sales total'),
                        'description' => __('View total sales by POS staff'),
                        'enabled' => true,
                    ],
                    [
                        'slug' => 'pos-staff-orders-total',
                        'name' => __('POS staff orders total'),
                        'description' => __('View order count by POS staff'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'pos-total-sales-by-product',
                        'name' => __('POS total sales by product'),
                        'description' => __('View POS sales by product'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'total-sales-by-pos-location',
                        'name' => __('Total sales by POS location'),
                        'description' => __('View sales by POS location/terminal'),
                        'enabled' => false,
                    ],
                ],
            ],
            'finances' => [
                'category' => 'Finances',
                'slug' => 'finances',
                'enabled' => true,
                'reports' => [
                    [
                        'slug' => 'payments-by-order',
                        'name' => __('Payments by order'),
                        'description' => __('View payment details by order'),
                        'enabled' => true,
                    ],
                    [
                        'slug' => 'gross-sales-by-order',
                        'name' => __('Gross sales by order'),
                        'description' => __('View gross sales per order'),
                        'enabled' => false,
                    ],
                    [
                        'slug' => 'total-sales-by-order',
                        'name' => __('Total sales by order'),
                        'description' => __('View total sales per order'),
                        'enabled' => true,
                    ],
                ],
            ],
            'inventory' => [
                'category' => 'Inventory',
                'slug' => 'inventory',
                'enabled' => true,
                'reports' => [
                    [
                        'slug' => 'purchase-orders-over-time',
                        'name' => __('Purchase orders over time'),
                        'description' => __('View purchase order trends over time'),
                        'enabled' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get all enabled (active) reports, each enriched with its category and category_slug.
     *
     * @return array
     */
    public static function getActiveReports()
    {
        $allReports = self::getAllReports();
        $activeReports = [];

        foreach ($allReports as $categorySlug => $category) {
            $activeCategoryReports = [];

            foreach ($category['reports'] as $report) {
                if ($report['enabled']) {
                    $report['category'] = $category['category'];
                    $report['category_slug'] = $category['slug'];
                    $activeCategoryReports[] = $report;
                }
            }

            if (! empty($activeCategoryReports)) {
                $activeReports[$categorySlug] = [
                    'category' => $category['category'],
                    'slug' => $category['slug'],
                    'reports' => $activeCategoryReports,
                ];
            }
        }

        return $activeReports;
    }

    /**
     * Get report by slug
     *
     * @param  string  $slug
     * @return array|null
     */
    public static function getReportBySlug($slug)
    {
        $allReports = self::getAllReports();

        foreach ($allReports as $category) {
            foreach ($category['reports'] as $report) {
                if ($report['slug'] === $slug) {
                    $report['category'] = $category['category'];
                    $report['category_slug'] = $category['slug'];

                    return $report;
                }
            }
        }

        return null;
    }

    /**
     * Get all reports flattened
     *
     * @return array
     */
    public static function getAllReportsFlat()
    {
        $allReports = self::getAllReports();
        $flat = [];

        foreach ($allReports as $category) {
            foreach ($category['reports'] as $report) {
                if (! $report['enabled']) {
                    continue;
                }
                $report['category'] = $category['category'];
                $report['category_slug'] = $category['slug'];
                $flat[] = $report;
            }
        }

        return $flat;
    }
}
