/**
 * Advance Report Module - Common Utilities
 * 
 * Shared utilities, configurations, and helper functions
 * 
 * @module AdvanceReport.Common
 * @version 1.0.0
 */

"use strict";

/**
 * Common configuration and utilities
 */
var AdvanceReportCommon = {
    
    /**
     * Table configuration
     * This object stores table-related settings that can be customized per report
     */
    tableConfig: {
        tableId: '#advanceReportTable',
        searchInputId: '#reportTableSearch',
        searchClearId: '#reportTableSearchClear',
        comparisonTableClass: 'comparison-table',
        emptyRowClass: 'report-empty-row',
        summaryRowDataAttr: 'data-row-type="summary"'
    },
    
    /**
     * Get column configuration for a report
     * Uses AdvanceReportConfig if available, otherwise falls back to legacy config
     * 
     * @param {string} slug - Report slug
     * @returns {Object} Column configuration
     */
    getColumnConfig: function(slug) {
        if (typeof AdvanceReportConfig !== 'undefined') {
            return AdvanceReportConfig.getColumnConfig(slug);
        }
        // Fallback to a sensible default if config is unavailable
        return {
            dateColumn: undefined,
            textColumns: [],
            numericColumns: [1, 2, 3]
        };
    },
    
    /**
     * Get search configuration for a report
     * Uses AdvanceReportConfig if available, otherwise falls back to legacy config
     * 
     * @param {string} slug - Report slug
     * @returns {Object} Search configuration
     */
    getSearchConfig: function(slug) {
        if (typeof AdvanceReportConfig !== 'undefined') {
            var config = AdvanceReportConfig.getSearchConfig(slug);
            return config.type || 'ajax';
        }
        // Fallback to default search type
        return 'ajax';
    },

    
    /**
     * Get current report slug from URL or page
     * 
     * @returns {string} Report slug
     */
    getCurrentReportSlug: function() {
        // Try to get from URL
        var pathParts = window.location.pathname.split('/');
        var slugIndex = pathParts.indexOf('advance-reports') + 1;
        if (slugIndex > 0 && pathParts[slugIndex]) {
            return pathParts[slugIndex];
        }
        
        // Default to 'default' if cannot determine
        return 'default';
    },
    
    /**
     * Escape HTML special characters to prevent XSS
     * 
     * @param {string} text - Text to escape
     * @returns {string} Escaped text safe for HTML insertion
     */
    escapeHtml: function(text) {
        if (typeof text !== 'string') {
            text = String(text);
        }
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) {
            return map[m];
        });
    },
    
    /**
     * Build error table HTML with dynamic column headers
     * 
     * @param {Array} columnHeaders - Array of column header text
     * @param {string} errorMessage - Error message to display
     * @returns {string} HTML string for error table
     */
    buildErrorTableHTML: function(columnHeaders, errorMessage) {
        var headers = '';
        var columnCount = columnHeaders.length;
        
        // Build table headers
        columnHeaders.forEach(function(header, index) {
            var alignClass = index === 0 ? '' : ' class="text-right"';
            headers += '<th' + alignClass + '>' + jsLang(header) + '</th>';
        });
        
        // Escape error message to prevent XSS
        var safeErrorMessage = this.escapeHtml(errorMessage);
        
        return '<div id="report">' +
            '<div class="table-responsive">' +
            '<table class="table advance-report-table display nowrap" id="advanceReportTable" style="width:100%">' +
            '<thead><tr>' + headers + '</tr></thead>' +
            '<tbody>' +
            '<tr class="report-empty-row">' +
            '<td colspan="' + columnCount + '">' +
            '<div class="report-no-data-message">' +
            '<div class="report-message-content">' +
            '<div class="report-message-icon-wrapper">' +
            '<i class="fa fa-exclamation-circle report-message-icon"></i>' +
            '</div>' +
            '<p class="report-message-text">' + safeErrorMessage + '</p>' +
            '</div>' +
            '</div>' +
            '</td>' +
            '</tr>' +
            '</tbody>' +
            '</table>' +
            '</div>' +
            '</div>';
    },
    
    /**
     * Get table column headers from the DOM
     * 
     * @param {jQuery} $table - The table jQuery object
     * @returns {Array} Array of column header text
     */
    getTableColumnHeaders: function($table) {
        var headers = [];
        $table.find('thead th').each(function() {
            var headerText = $(this).text().trim();
            // Remove translation markers if present
            headerText = headerText.replace(/__\(['"](.+?)['"]\)/g, '$1');
            headers.push(headerText);
        });
        return headers;
    },
    
    /**
     * Update date range display in the input field
     * 
     * @param {moment} startDate - Start date moment object
     * @param {moment} endDate - End date moment object
     */
    updateDateRangeDisplay: function(startDate, endDate) {
        var start = moment(startDate);
        var end = moment(endDate);
        // Format as YYYY-MM-DD
        var displayText = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
        $('#daterange-btn').val(displayText);
    },
    
    /**
     * Register custom DataTable sorting types for HTML content in cells
     * This allows proper sorting of formatted numbers and dates in HTML
     */
    registerCustomSortingTypes: function() {
        // Check if DataTables is loaded
        if (typeof jQuery.fn.dataTable === 'undefined' || 
            typeof jQuery.fn.dataTable.ext === 'undefined' || 
            typeof jQuery.fn.dataTable.ext.type === 'undefined' || 
            typeof jQuery.fn.dataTable.ext.type.order === 'undefined') {
            // DataTables not loaded yet - skip registration
            return;
        }
        
        // Check if already registered (avoid duplicate registration)
        if (jQuery.fn.dataTable.ext.type.order['html-string-pre']) {
            return;
        }
        
        // String sorting with HTML - extracts text from HTML content
        jQuery.extend(jQuery.fn.dataTable.ext.type.order, {
            "html-string-pre": function(data) {
                var $cell = $('<div>').html(data);
                var sortValue = $cell.find('[data-sort]').first().attr('data-sort');
                if (sortValue !== undefined) {
                    return sortValue;
                }
                // Extract text from first div if no data-sort attribute
                var firstDiv = $cell.find('div').first().text().trim();
                return firstDiv || String(data).replace(/<[\s\S]*?>/g, "").trim();
            },
            "html-string-asc": function(a, b) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },
            "html-string-desc": function(a, b) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            },
            
            // Numeric sorting with HTML - extracts numbers from formatted text
            "html-numeric-pre": function(data) {
                var $cell = $('<div>').html(data);
                var sortValue = $cell.find('[data-sort]').first().attr('data-sort');
                if (sortValue !== undefined) {
                    return parseFloat(sortValue) || 0;
                }
                // Extract numeric value from HTML
                var text = String(data).replace(/<[\s\S]*?>/g, "");
                // Remove formatting characters (commas, currency symbols, etc.)
                text = text.replace(/[^\d.-]/g, "");
                return parseFloat(text) || 0;
            },
            "html-numeric-asc": function(a, b) {
                return a - b;
            },
            "html-numeric-desc": function(a, b) {
                return b - a;
            },
            
            // Date sorting with HTML - parses dates from HTML content
            "html-date-pre": function(data) {
                var $cell = $('<div>').html(data);
                var sortValue = $cell.find('[data-sort]').first().attr('data-sort');
                if (sortValue !== undefined) {
                    // Try to parse the date string
                    var date = moment(sortValue);
                    if (date.isValid()) {
                        return date.valueOf();
                    }
                    // Fallback to native Date parsing
                    var parsed = Date.parse(sortValue);
                    return isNaN(parsed) ? 0 : parsed;
                }
                // Extract date from first div
                var firstDiv = $cell.find('div').first().text().trim();
                if (!firstDiv) return 0;
                var date = moment(firstDiv);
                if (date.isValid()) {
                    return date.valueOf();
                }
                var parsed = Date.parse(firstDiv);
                return isNaN(parsed) ? 0 : parsed;
            },
            "html-date-asc": function(a, b) {
                return a - b;
            },
            "html-date-desc": function(a, b) {
                return b - a;
            }
        });
    },
    
    /**
     * Update export button URL with current filter parameters
     * This ensures the export CSV button always uses the current filter values
     */
    updateExportButtonUrl: function() {
        var $exportBtn = $('#exportBtn');
        if (!$exportBtn.length) {
            return;
        }
        
        // Get current report slug
        var reportSlug = this.getCurrentReportSlug();
        
        // Build export base URL from current page URL
        var currentPath = window.location.pathname;
        var exportBaseUrl = currentPath.replace(/\/$/, '') + '/export';
        
        // Build query parameters from current form values and URL
        var params = {};
        
        // Get date range
        var fromDate = $('#startfrom').val();
        var toDate = $('#endto').val();
        if (fromDate) params.from = fromDate;
        if (toDate) params.to = toDate;
        
        // Get vendor
        var vendorId = $('#vendor_id').val();
        if (vendorId) params.vendor_id = vendorId;
        
        // Get payment status
        var paymentStatus = $('#payment_status').val();
        if (paymentStatus) params.payment_status = paymentStatus;
        
        // Get order status
        var orderStatus = $('#order_status').val();
        if (orderStatus) params.order_status = orderStatus;
        
        // Get channel
        var channel = $('#channel').val();
        if (channel) params.channel = channel;
        
        // Get search value
        var search = $('#reportTableSearch').val();
        if (search) params.search = search;
        
        // Get sort parameters from URL
        var urlParams = new URLSearchParams(window.location.search);
        var sortColumn = urlParams.get('sort_column');
        var sortDirection = urlParams.get('sort_direction');
        if (sortColumn) params.sort_column = sortColumn;
        if (sortDirection) params.sort_direction = sortDirection;
        
        // Build query string
        var queryString = Object.keys(params).map(function(key) {
            return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
        }).join('&');
        
        // Update export button href
        $exportBtn.attr('href', exportBaseUrl + (queryString ? '?' + queryString : ''));
    }
};

