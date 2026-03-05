/**
 * Advance Report Module - Report Configuration
 * 
 * Centralized configuration file for all reports
 * To create a new report, simply add a new entry to the reports object
 * 
 * @module AdvanceReport.Config
 * @version 1.0.0
 */

"use strict";

/**
 * Report Configuration
 * 
 * Configuration structure for each report:
 * - slug: Report identifier (must match backend slug)
 * - columns: Column configuration
 *   - dateColumn: Index of date column (optional, undefined if no date column)
 *   - textColumns: Array of column indices that are text columns
 *   - numericColumns: Array of column indices that are numeric columns
 * - search: Search configuration
 *   - type: 'ajax' for server-side search, 'jquery' for client-side search
 * - sorting: Sorting configuration
 *   - enabled: Whether sorting is enabled (default: true)
 *   - defaultColumn: Default column index to sort by (default: 0)
 *   - defaultDirection: 'asc' or 'desc' (default: 'asc')
 * - pagination: Pagination configuration
 *   - enabled: Whether pagination is enabled (default: false)
 *   - perPage: Number of items per page (default: 25)
 * - stickyHeader: Sticky header configuration
 *   - enabled: Whether sticky header sorting sync is enabled (default: false)
 * - datatables: DataTables initialization configuration
 *   - enabled: Whether DataTables should be initialized (default: true)
 *   - Set to false for reports that use AJAX pagination instead of DataTables
 * - sorting: Sorting configuration
 *   - ajaxSorting: Whether to use AJAX-based sorting (default: false)
 *   - Set to true for reports that need custom AJAX sorting handlers instead of DataTables sorting
 * - features: Additional features
 *   - summaryRow: Whether report has summary row that should stay at top (default: false)
 */
var AdvanceReportConfig = {
    
    /**
     * Report configurations
     * Add new reports here by following the structure below
     */
    reports: {
        // Total Sales Over Time Report
        'total-sales-over-time': {
            slug: 'total-sales-over-time',
            columns: {
                dateColumn: 0, // Date column index
                textColumns: [], // No text columns
                numericColumns: [1, 2, 3] // Number of Orders, Total Sales, Average Order Value
            },
            search: {
                type: 'jquery' // Client-side search using DataTables
            },
            sorting: {
                enabled: true,
                defaultColumn: 0, // Sort by date column
                defaultColumnName: 'date', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Most recent first
                ajaxSorting: false // Use DataTables sorting
            },
            pagination: {
                enabled: false // Show all data
            },
            stickyHeader: {
                enabled: true // Enable sticky header sorting sync
            },
            datatables: {
                enabled: true // Initialize DataTables for client-side operations
            },
            features: {
                summaryRow: true // Has summary row that should stay at top
            }
        },
        
        // Purchase Orders Over Time Report
        'purchase-orders-over-time': {
            slug: 'purchase-orders-over-time',
            columns: {
                dateColumn: 0, // Date column index
                textColumns: [], // No text columns
                numericColumns: [1, 2, 3, 4] // Number of Purchase Orders, Total Product, Total Amount, Average Purchase Value
            },
            search: {
                type: 'jquery' // Client-side search using DataTables
            },
            sorting: {
                enabled: true,
                defaultColumn: 0, // Sort by date column
                defaultColumnName: 'date', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Most recent first
                ajaxSorting: false // Use DataTables sorting
            },
            pagination: {
                enabled: false // Show all data
            },
            stickyHeader: {
                enabled: true // Enable sticky header sorting sync
            },
            datatables: {
                enabled: true // Initialize DataTables for client-side operations
            },
            features: {
                summaryRow: true // Has summary row that should stay at top
            }
        },
        
        // Total Sales By Product Report
        'total-sales-by-product': {
            slug: 'total-sales-by-product',
            columns: {
                dateColumn: undefined, // No date column
                textColumns: [0, 1], // Product name (0), Vendor name (1)
                numericColumns: [2, 3, 4, 5, 6], // Quantity sold, Number of orders, Total sales, Tax, Average sale price
                firstColumnWidth: 350 // First column width in pixels
            },
            search: {
                type: 'ajax' // Server-side search with AJAX
            },
            sorting: {
                enabled: true,
                defaultColumn: 4, // Sort by Total Sales column
                defaultColumnName: 'total_sales', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Highest sales first
                ajaxSorting: true // Use AJAX-based sorting instead of DataTables sorting
            },
            pagination: {
                enabled: true, // Enable pagination
                perPage: 25
            },
            stickyHeader: {
                enabled: false // No sticky header for this report
            },
            datatables: {
                enabled: false // Don't initialize DataTables - uses AJAX pagination
            },
            features: {
                summaryRow: true // Has summary row
            }
        },
        
        // Sales By Customer Name Report
        'sales-by-customer-name': {
            slug: 'sales-by-customer-name',
            columns: {
                dateColumn: undefined, // No date column
                textColumns: [0, 1, 2], // Customer name (0), Email (1), Vendor (2)
                numericColumns: [3, 4, 5, 6, 7], // Total quantity, Number of orders, Total sales, Total paid, Average order value
                firstColumnWidth: 350
            },
            search: {
                type: 'ajax' // Server-side search with AJAX
            },
            sorting: {
                enabled: true,
                defaultColumn: 4, // Sort by Total Sales column
                defaultColumnName: 'total_sales', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Highest sales first
                ajaxSorting: true // Use AJAX-based sorting instead of DataTables sorting
            },
            pagination: {
                enabled: true, // Enable pagination
                perPage: 25
            },
            stickyHeader: {
                enabled: false // No sticky header for this report
            },
            datatables: {
                enabled: false // Don't initialize DataTables - uses AJAX pagination
            },
            features: {
                summaryRow: true // Has summary row
            }
        },
        
        // Payments By Order Report
        'payments-by-order': {
            slug: 'payments-by-order',
            columns: {
                dateColumn: 1, // Order Date column (1)
                textColumns: [0, 2, 3, 6], // Order Reference (0), Customer name (2), Email (3), Status (6)
                numericColumns: [4, 5] // Order Total (4), Order Paid (5)
            },
            search: {
                type: 'ajax' // Server-side search with AJAX
            },
            sorting: {
                enabled: true,
                defaultColumn: 1, // Sort by Order Date column
                defaultColumnName: 'order_date', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Most recent first
                ajaxSorting: true // Use AJAX-based sorting instead of DataTables sorting
            },
            pagination: {
                enabled: true, // Enable pagination
                perPage: 25
            },
            stickyHeader: {
                enabled: false // No sticky header for this report
            },
            datatables: {
                enabled: false // Don't initialize DataTables - uses AJAX pagination
            },
            features: {
                summaryRow: true // Has summary row
            }
        },
        'total-sales-by-order': {
            slug: 'total-sales-by-order',
            columns: {
                dateColumn: 0, // Order Date column (0)
                textColumns: [1, 2, 6, 7, 8], // Reference (1), Customer (2), Channel (6), Payment Status (7), Order Status (8)
                numericColumns: [3, 4, 5] // Total Quantity (3), Total (4), Paid (5)
            },
            search: {
                type: 'ajax' // Server-side search with AJAX
            },
            sorting: {
                enabled: true,
                defaultColumn: 0, // Sort by Order Date column
                defaultColumnName: 'order_date', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Most recent first
                ajaxSorting: true // Use AJAX-based sorting instead of DataTables sorting
            },
            pagination: {
                enabled: true, // Enable pagination
                perPage: 25
            },
            stickyHeader: {
                enabled: false // No sticky header for this report
            },
            datatables: {
                enabled: false // Don't initialize DataTables - uses AJAX pagination
            },
            features: {
                summaryRow: true // Has summary row
            }
        },
        'pos-staff-sales-total': {
            slug: 'pos-staff-sales-total',
            columns: {
                dateColumn: undefined, // No date column
                textColumns: [0], // Staff Name (0)
                numericColumns: [1, 2, 3, 4], // Total Orders (1), Average Sales (2), Order Item Quantity (3), Total Sales (4)
                firstColumnWidth: 350
            },
            search: {
                type: 'ajax' // Server-side search with AJAX
            },
            sorting: {
                enabled: true,
                defaultColumn: 4, // Sort by Total Sales column
                defaultColumnName: 'total_sales', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Highest sales first
                ajaxSorting: true // Use AJAX-based sorting instead of DataTables sorting
            },
            pagination: {
                enabled: true, // Enable pagination
                perPage: 25
            },
            stickyHeader: {
                enabled: false // No sticky header for this report
            },
            datatables: {
                enabled: false // Don't initialize DataTables - uses AJAX pagination
            },
            features: {
                summaryRow: true // Has summary row
            }
        },
        'pos-staff-daily-sales-total': {
            slug: 'pos-staff-daily-sales-total',
            columns: {
                dateColumn: 0, // Date column index
                textColumns: [], // No text columns
                numericColumns: [1, 2, 3] // Number of Orders, Total Sales, Average Order Value
            },
            search: {
                type: 'jquery' // Client-side search using DataTables
            },
            sorting: {
                enabled: true,
                defaultColumn: 0, // Sort by date column
                defaultColumnName: 'date', // Default sort column name for AJAX requests
                defaultDirection: 'desc', // Most recent first
                ajaxSorting: false // Use DataTables sorting
            },
            pagination: {
                enabled: false // Show all data
            },
            stickyHeader: {
                enabled: true // Enable sticky header sorting sync
            },
            datatables: {
                enabled: true // Initialize DataTables for client-side operations
            },
            features: {
                summaryRow: true // Has summary row that should stay at top
            }
        }
    },
    
    /**
     * Get configuration for a specific report
     * 
     * @param {string} slug - Report slug
     * @returns {Object} Report configuration or default configuration
     */
    getReportConfig: function(slug) {
        if (!slug || !this.reports[slug]) {
            return this.getDefaultConfig();
        }
        return this.reports[slug];
    },
    
    /**
     * Get default configuration
     * Used when a report doesn't have a specific configuration
     * 
     * @returns {Object} Default configuration
     */
    getDefaultConfig: function() {
        return {
            slug: 'default',
            columns: {
                dateColumn: undefined,
                textColumns: [],
                numericColumns: [1, 2, 3],
                firstColumnWidth: 200 // Default first column width in pixels
            },
            search: {
                type: 'ajax'
            },
            sorting: {
                enabled: true,
                defaultColumn: 0,
                defaultColumnName: 'total_sales', // Default sort column name for AJAX requests
                defaultDirection: 'asc',
                ajaxSorting: false // Default: use DataTables sorting
            },
            pagination: {
                enabled: false,
                perPage: 25
            },
            stickyHeader: {
                enabled: false
            },
            datatables: {
                enabled: true // Default: enable DataTables
            },
            features: {
                summaryRow: false
            }
        };
    },
    
    /**
     * Check if DataTables should be enabled for a report
     * 
     * @param {string} slug - Report slug
     * @returns {boolean} True if DataTables should be enabled
     */
    isDataTablesEnabled: function(slug) {
        var config = this.getReportConfig(slug);
        return config.datatables && config.datatables.enabled !== false;
    },
    
    /**
     * Check if AJAX sorting should be enabled for a report
     * 
     * @param {string} slug - Report slug
     * @returns {boolean} True if AJAX sorting should be enabled
     */
    isAjaxSortingEnabled: function(slug) {
        var config = this.getReportConfig(slug);
        return config.sorting && config.sorting.ajaxSorting === true;
    },
    
    /**
     * Get column configuration for a report
     * 
     * @param {string} slug - Report slug
     * @returns {Object} Column configuration
     */
    getColumnConfig: function(slug) {
        var config = this.getReportConfig(slug);
        return config.columns || this.getDefaultConfig().columns;
    },
    
    /**
     * Get search configuration for a report
     * 
     * @param {string} slug - Report slug
     * @returns {Object} Search configuration
     */
    getSearchConfig: function(slug) {
        var config = this.getReportConfig(slug);
        return config.search || this.getDefaultConfig().search;
    },
    
    /**
     * Get sorting configuration for a report
     * 
     * @param {string} slug - Report slug
     * @returns {Object} Sorting configuration
     */
    getSortingConfig: function(slug) {
        var config = this.getReportConfig(slug);
        return config.sorting || this.getDefaultConfig().sorting;
    },
    
    /**
     * Get default sort column name for a report
     * Used for AJAX requests to determine which column to sort by default
     * 
     * @param {string} slug - Report slug
     * @returns {string} Default sort column name (e.g., 'order_date', 'total_sales')
     */
    getDefaultSortColumnName: function(slug) {
        var sortingConfig = this.getSortingConfig(slug);
        return sortingConfig.defaultColumnName || 'total_sales';
    },
    
    /**
     * Get pagination configuration for a report
     * 
     * @param {string} slug - Report slug
     * @returns {Object} Pagination configuration
     */
    getPaginationConfig: function(slug) {
        var config = this.getReportConfig(slug);
        return config.pagination || this.getDefaultConfig().pagination;
    },
    
    /**
     * Check if sticky header is enabled for a report
     * 
     * @param {string} slug - Report slug
     * @returns {boolean} True if sticky header is enabled
     */
    isStickyHeaderEnabled: function(slug) {
        var config = this.getReportConfig(slug);
        return config.stickyHeader && config.stickyHeader.enabled === true;
    },
    
    /**
     * Check if summary row feature is enabled for a report
     * 
     * @param {string} slug - Report slug
     * @returns {boolean} True if summary row should stay at top
     */
    hasSummaryRow: function(slug) {
        var config = this.getReportConfig(slug);
        return config.features && config.features.summaryRow === true;
    },
    
    /**
     * Get first column width for a report
     * 
     * @param {string} slug - Report slug
     * @returns {number} First column width in pixels (default: 200)
     */
    getFirstColumnWidth: function(slug) {
        var config = this.getReportConfig(slug);
        var columnConfig = config.columns || {};
        return columnConfig.firstColumnWidth !== undefined ? columnConfig.firstColumnWidth : 200;
    }
};

