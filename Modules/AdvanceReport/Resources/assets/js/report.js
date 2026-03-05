/**
 * Advance Report Module - Main Entry Point
 * 
 * Main application file that initializes all modules
 * 
 * @module AdvanceReport
 * @version 1.0.0
 */

"use strict";

/**
 * Main application object that handles all report functionality
 */
var AdvanceReportApp = {
    
    /**
     * Initialize the application
     */
    init: function() {
        var self = this;
        
        $(document).ready(function() {
            // Initialize date range picker (for show page)
            AdvanceReportShow.initializeDateRangePicker();
            
            // Apply first column width on page load
            var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
            var $mainTable = $('#mainTable');
            if ($mainTable.length && reportSlug) {
                AdvanceReportTable.applyFirstColumnWidth($mainTable, reportSlug);
            }
            
            // Initialize DataTable (will register custom sorting types if needed)
            AdvanceReportTable.initializeDataTable();
            
            // Bind pagination handler (for show page)
            AdvanceReportShow.bindPagination();
            
            // Initialize table sorting (for product report)
            AdvanceReportShow.initializeTableSorting();
            
            // Bind form submission handler (for show page)
            AdvanceReportShow.bindFormSubmission();
            
            // Initialize search based on report configuration
            var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
            var searchType = AdvanceReportCommon.getSearchConfig(reportSlug);
            
            if (searchType === 'jquery') {
                // Will be initialized after DataTable is ready
            } else {
                // Initialize AJAX search (for reports that use server-side search)
                AdvanceReportTable.initializeAjaxSearch();
            }
            
            // Initialize category dropdown (for index page)
            AdvanceReportList.initializeCategoryDropdown();
            
            // Initialize sticky header (for show page)
            AdvanceReportShow.initializeStickyHeader();
            
            // Bind filter change handlers to update export button
            AdvanceReportShow.bindFilterChangeHandlers();
            
            // Update export button URL on initial page load
            AdvanceReportCommon.updateExportButtonUrl();
        });
    }
};

// Initialize the application
AdvanceReportApp.init();
