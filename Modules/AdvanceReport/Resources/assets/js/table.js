/**
 * Advance Report Module - Table Management
 * 
 * DataTables initialization, search functionality, and table-related operations
 * 
 * @module AdvanceReport.Table
 * @version 1.0.0
 */

"use strict";

/**
 * Table management functionality
 */
var AdvanceReportTable = {
    
    /**
     * Current report table instance
     */
    reportTable: null,
    
    /**
     * Previous search value for comparison
     */
    previousSearchValue: '',
    
    /**
     * Get column configuration for current report
     * This method determines the column structure based on report type
     * 
     * @param {jQuery} $table - The table jQuery object
     * @returns {Object} Column configuration object
     */
    getColumnConfiguration: function($table) {
        var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
        var config = AdvanceReportCommon.getColumnConfig(reportSlug);
        var isComparisonTable = $table.hasClass(AdvanceReportCommon.tableConfig.comparisonTableClass);
        
        // Build column definitions based on configuration
        var columnDefs = [];
        
        // Date column configuration
        if (config.dateColumn !== undefined) {
            columnDefs.push({
                type: 'html-string',
                targets: config.dateColumn
            });
        }
        
        // Numeric columns configuration
        if (config.numericColumns && config.numericColumns.length > 0) {
            columnDefs.push({
                type: 'html-numeric',
                className: 'text-right',
                targets: config.numericColumns
            });
        }
        
        // Text columns configuration
        if (config.textColumns && config.textColumns.length > 0) {
            config.textColumns.forEach(function(colIndex) {
                columnDefs.push({
                    type: 'html-string',
                    targets: colIndex
                });
            });
        }
        
        return columnDefs;
    },
    
    /**
     * Destroy DataTable instance safely
     */
    destroyDataTable: function() {
        // Destroy mainTable if it exists
        if ($('#mainTable').length && $.fn.DataTable.isDataTable('#mainTable')) {
            try {
                $('#mainTable').DataTable().destroy();
            } catch(e) {
                // Ignore errors
            }
        }
        
        // Destroy advanceReportTable if it exists
        if ($(AdvanceReportCommon.tableConfig.tableId).length && $.fn.DataTable.isDataTable(AdvanceReportCommon.tableConfig.tableId)) {
            try {
                $(AdvanceReportCommon.tableConfig.tableId).DataTable().destroy();
            } catch(e) {
                // Ignore errors
            }
        }
        
        // Clear reportTable reference
        if (this.reportTable) {
            try {
                this.reportTable.destroy();
            } catch(e) {
                // Ignore errors
            }
            this.reportTable = null;
        }
    },
    
    /**
     * Apply first column width based on report configuration
     * 
     * @param {jQuery} $table - The table jQuery object
     * @param {string} reportSlug - Report slug
     */
    applyFirstColumnWidth: function($table, reportSlug) {
        if (typeof AdvanceReportConfig === 'undefined') {
            return;
        }
        
        var firstColumnWidth = AdvanceReportConfig.getFirstColumnWidth(reportSlug);
        var $firstColumn = $table.find('thead th:first-child, tbody td:first-child');
        
        if ($firstColumn.length) {
            $firstColumn.css({
                'min-width': firstColumnWidth + 'px',
                'width': firstColumnWidth + 'px',
                'max-width': firstColumnWidth + 'px'
            });
        }
        
        // Also apply to sticky header table if it exists
        var $stickyTable = $('#advanceReportTable');
        if ($stickyTable.length) {
            var $stickyFirstColumn = $stickyTable.find('thead th:first-child');
            if ($stickyFirstColumn.length) {
                $stickyFirstColumn.css({
                    'min-width': firstColumnWidth + 'px',
                    'width': firstColumnWidth + 'px',
                    'max-width': firstColumnWidth + 'px'
                });
            }
        }
    },
    
    /**
     * Initialize DataTable with appropriate configuration
     */
    initializeDataTable: function() {
        var self = this;
        
        // Get the correct table ID based on report structure
        // For reports with sticky header, use #mainTable (the actual data table)
        // Otherwise use #advanceReportTable
        var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
        var tableSelector = '#mainTable';
        var $table = $(tableSelector);
        
        // If mainTable doesn't exist, try advanceReportTable
        if (!$table.length) {
            tableSelector = AdvanceReportCommon.tableConfig.tableId;
            $table = $(tableSelector);
        }
        
        // Check if table exists and has valid structure
        if (!$table.length) {
            return;
        }
        
        // Check if table has thead and tbody
        if ($table.find('thead').length === 0 || $table.find('tbody').length === 0) {
            return;
        }
        
        // Destroy any existing DataTable instance first
        this.destroyDataTable();
        
        // Also destroy on mainTable if it exists
        if ($('#mainTable').length && $.fn.DataTable.isDataTable('#mainTable')) {
            try {
                $('#mainTable').DataTable().destroy();
            } catch(e) {
                // Ignore errors
            }
        }
        
        // Wait a bit to ensure DOM is ready
        setTimeout(function() {
            try {
                // Register custom sorting types before initializing DataTable
                AdvanceReportCommon.registerCustomSortingTypes();
                
                // Check again if table still exists
                if (!$table.length || $.fn.DataTable.isDataTable(tableSelector)) {
                    return;
                }
                
                var $tbody = $table.find('tbody');
                var $rows = $tbody.find('tr');
                var $emptyRow = $tbody.find('.' + AdvanceReportCommon.tableConfig.emptyRowClass.replace('#', ''));
                
                // Apply first column width for non-DataTables reports (AJAX pagination)
                if (!AdvanceReportConfig.isDataTablesEnabled(reportSlug)) {
                    // Don't initialize DataTables - report uses AJAX pagination
                    // But still apply first column width
                    self.applyFirstColumnWidth($table, reportSlug);
                    return;
                }
                
                if ($tbody.length && $rows.length > 0) {
                    // Check if table has message row (empty row) - handle differently
                    if ($emptyRow.length > 0) {
                        // For message rows, disable all features
                        self.reportTable = $table.DataTable({
                            responsive: true,
                            ordering: false,
                            paging: false,
                            searching: false,
                            info: false,
                            columnDefs: [
                                { orderable: false, targets: '_all' }
                            ]
                        });
                        
                        // Keep empty row visible
                        var fixedRow = $table.find('tbody tr:first').clone().addClass('fixed-row');
                        self.reportTable.on('draw', function () {
                            var hasFixed = $(AdvanceReportCommon.tableConfig.tableId + ' tbody tr.fixed-row').length;
                            if (!hasFixed) {
                                $(fixedRow).addClass('fixed-row');
                                $(AdvanceReportCommon.tableConfig.tableId + ' tbody').prepend(fixedRow);
                            }
                        });
                    } else {
                        // Normal data table initialization - No pagination, show all data
                        var columnDefs = self.getColumnConfiguration($table);
                        
                        // Get first column width and add to columnDefs if configured
                        var firstColumnWidth = typeof AdvanceReportConfig !== 'undefined' 
                            ? AdvanceReportConfig.getFirstColumnWidth(reportSlug)
                            : 200;
                        
                        // Add first column width to columnDefs if it's not the default
                        if (firstColumnWidth !== 200) {
                            columnDefs.push({
                                targets: 0,
                                width: firstColumnWidth + 'px',
                                className: 'text-left'
                            });
                        }
                        
                        // Get sorting configuration from config
                        var sortingConfig = typeof AdvanceReportConfig !== 'undefined' 
                            ? AdvanceReportConfig.getSortingConfig(reportSlug)
                            : { enabled: true, defaultColumn: 0, defaultDirection: 'asc' };
                        
                        var defaultSortOrder = sortingConfig.defaultDirection || 'asc';
                        var defaultSortColumn = sortingConfig.defaultColumn !== undefined ? sortingConfig.defaultColumn : 0;
                        
                        self.reportTable = $table.DataTable({
                            responsive: false,
                            ordering: sortingConfig.enabled !== false,
                            paging: false,
                            searching: true,
                            dom: 'rti', // Remove default search box
                            info: true,
                            order: [[defaultSortColumn, defaultSortOrder]],
                            language: {
                                info: jsLang('Showing _TOTAL_ entries'),
                                infoEmpty: jsLang('No entries to show'),
                                infoFiltered: jsLang('(filtered from _MAX_ total entries)'),
                            },
                            columnDefs: columnDefs,
                            footerCallback: function (row, data, start, end, display) {
                                // Keep footer visible even with DataTable
                            },
                            initComplete: function(settings, json) {
                                // Move DataTables info element outside scrollable container
                                var self = this;
                                setTimeout(function() {
                                    var $tableWrapper = $table.closest('.table-wrapper');
                                    var $scrollContainer = $table.closest('.table-scroll-container');
                                    var $infoElement = $tableWrapper.find('.dataTables_info');
                                    
                                    if ($infoElement.length && $scrollContainer.length) {
                                        // Move info element outside scroll container but inside table wrapper
                                        $infoElement.detach().appendTo($tableWrapper);
                                    }
                                    
                                    // Apply first column width from config
                                    AdvanceReportTable.applyFirstColumnWidth($table, reportSlug);
                                }, 50);
                            },
                            drawCallback: function(settings) {
                                // Keep summary row at the top after sorting/drawing
                                var $tbody = $table.find('tbody');
                                var $summaryRow = $tbody.find('tr[' + AdvanceReportCommon.tableConfig.summaryRowDataAttr.split('=')[0] + '="summary"]');
                                if ($summaryRow.length) {
                                    $summaryRow.detach();
                                    $tbody.prepend($summaryRow);
                                }
                                
                                // Ensure info element stays outside scrollable container
                                var $tableWrapper = $table.closest('.table-wrapper');
                                var $scrollContainer = $table.closest('.table-scroll-container');
                                var $infoElement = $tableWrapper.find('.dataTables_info');
                                
                                if ($infoElement.length && $scrollContainer.length) {
                                    // Check if info is inside scroll container (should be moved outside)
                                    var $infoInScroll = $scrollContainer.find('.dataTables_info');
                                    if ($infoInScroll.length) {
                                        $infoInScroll.detach().appendTo($tableWrapper);
                                    }
                                }
                                
                                // Reapply first column width after redraw
                                AdvanceReportTable.applyFirstColumnWidth($table, reportSlug);
                            }
                        });
                        
                        // Initialize search functionality based on report configuration
                        var searchType = AdvanceReportCommon.getSearchConfig(reportSlug);
                        
                        if (searchType === 'jquery') {
                            // Use jQuery/DataTables client-side search
                            // Small delay to ensure DataTable is fully initialized
                            setTimeout(function() {
                                self.connectSearchFunctionality();
                            }, 100);
                        } else {
                            // Use AJAX server-side search
                            self.initializeAjaxSearch();
                        }
                        
                        // Ensure summary row stays at top after any column sorting
                        $table.on('order.dt', function() {
                            setTimeout(function() {
                                var $tbody = $table.find('tbody');
                                var $summaryRow = $tbody.find('tr[data-row-type="summary"]');
                                if ($summaryRow.length) {
                                    $summaryRow.detach();
                                    $tbody.prepend($summaryRow);
                                }
                            }, 10);
                        });
                    }
                }
            } catch(e) {
                console.error('DataTable initialization error:', e);
                self.reportTable = null;
            }
        }, 150);
    },
    
    /**
     * Connect search input functionality to DataTable
     * Uses jQuery/DataTables client-side search
     */
    connectSearchFunctionality: function() {
        var self = this;
        var $searchInput = $(AdvanceReportCommon.tableConfig.searchInputId);
        var $searchClear = $(AdvanceReportCommon.tableConfig.searchClearId);
        
        if (!$searchInput.length) {
            return;
        }
        
        function getDataTableInstance() {
            // Try to get the DataTable instance from reportTable first
            if (self.reportTable) {
                return self.reportTable;
            }
            
            // Try to get from #mainTable (for reports with sticky header)
            if ($('#mainTable').length && $.fn.DataTable.isDataTable('#mainTable')) {
                return $('#mainTable').DataTable();
            }
            
            // Try to get from advanceReportTable
            if ($(AdvanceReportCommon.tableConfig.tableId).length && $.fn.DataTable.isDataTable(AdvanceReportCommon.tableConfig.tableId)) {
                return $(AdvanceReportCommon.tableConfig.tableId).DataTable();
            }
            
            return null;
        }
        
        function performSearch() {
            var searchValue = $searchInput.val();
            var dataTable = getDataTableInstance();
            
            if (dataTable) {
                dataTable.search(searchValue).draw();
            }
            
            // Show/hide clear button
            if (searchValue.length > 0) {
                $searchClear.fadeIn(200);
            } else {
                $searchClear.fadeOut(200);
            }
        }
        
        // Remove existing handlers to prevent duplicates
        $searchInput.off('keyup.reportSearch input.reportSearch paste.reportSearch keydown.reportSearch');
        $searchClear.off('click.reportSearch');
        $(document).off('searchReconnect.reportSearch');
        
        // Search on input
        $searchInput.on('keyup.reportSearch input.reportSearch paste.reportSearch', function() {
            performSearch();
        });
        
        // Clear search button
        $searchClear.on('click.reportSearch', function(e) {
            e.preventDefault();
            $searchInput.val('').focus();
            performSearch();
        });
        
        // Clear search on Escape key
        $searchInput.on('keydown.reportSearch', function(e) {
            if (e.key === 'Escape') {
                $(this).val('');
                performSearch();
            }
        });
        
        // Reconnect search after AJAX update
        $(document).on('searchReconnect.reportSearch', function() {
            var dataTable = getDataTableInstance();
            if (dataTable && $searchInput.length) {
                var currentValue = $searchInput.val();
                if (currentValue) {
                    dataTable.search(currentValue).draw();
                    $searchClear.fadeIn(200);
                } else {
                    $searchClear.fadeOut(200);
                }
            }
        });
    },
    
    /**
     * Initialize AJAX search functionality
     * Replaces DataTables client-side search with server-side AJAX search
     */
    initializeAjaxSearch: function() {
        var self = this;
        var $searchInput = $(AdvanceReportCommon.tableConfig.searchInputId);
        var $searchClear = $(AdvanceReportCommon.tableConfig.searchClearId);
        var searchTimeout = null;
        
        if (!$searchInput.length) {
            return;
        }
        
        // Initialize previous search value from current search value or URL parameter
        if (self.previousSearchValue === '') {
            var currentValue = $searchInput.val() || '';
            if (!currentValue && typeof currentSearch !== 'undefined') {
                currentValue = currentSearch || '';
            }
            if (!currentValue) {
                // Try to get from URL
                var urlParams = new URLSearchParams(window.location.search);
                currentValue = urlParams.get('search') || '';
            }
            self.previousSearchValue = currentValue;
            $searchInput.val(currentValue);
            
            // Show/hide clear button based on initial value
            if (currentValue.length > 0) {
                $searchClear.fadeIn(200);
            } else {
                $searchClear.fadeOut(200);
            }
        }
        
        // Remove existing handlers to prevent duplicates
        $searchInput.off('keyup.reportSearch input.reportSearch paste.reportSearch keydown.reportSearch');
        $searchClear.off('click.reportSearch');
        
        // Perform AJAX search
        function performAjaxSearch(searchValue) {
            var $reportContent = $('#report-content');
            var $reportLoadingOverlay = $('#report-loading-overlay');
            
            // Show loading overlay
            if ($reportLoadingOverlay.length) {
                $reportLoadingOverlay.show();
            } else {
                var loadingHTML = '<div id="report-loading-overlay" class="report-loading-overlay">' +
                    '<div class="loading-spinner">' +
                    '<div class="spinner-border text-primary" role="status">' +
                    '<span class="sr-only">' + jsLang('Loading') + '...</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                $reportContent.append(loadingHTML);
                $reportLoadingOverlay = $('#report-loading-overlay');
                $reportLoadingOverlay.show();
            }
            
            // Get current sort parameters
            var urlParams = new URLSearchParams(window.location.search);
            var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
            var defaultSortColumn = typeof AdvanceReportConfig !== 'undefined' 
                ? AdvanceReportConfig.getDefaultSortColumnName(reportSlug)
                : 'total_sales';
            var sortColumn = urlParams.get('sort_column') || defaultSortColumn;
            var sortDirection = urlParams.get('sort_direction') || 'desc';
            
            // Get form data
            var formData = {
                from: $('#startfrom').val(),
                to: $('#endto').val(),
                vendor_id: $('#vendor_id').val() || '',
                payment_status: $('#payment_status').val() || '',
                order_status: $('#order_status').val() || '',
                channel: $('#channel').val() || '',
                search: searchValue || '',
                page: 1, // Reset to first page on search
                per_page: 25,
                sort_column: sortColumn,
                sort_direction: sortDirection
            };
            
            // Get report slug from URL
            var url = window.location.origin + window.location.pathname;
            
            // Update URL with search parameter
            var urlObj = new URL(window.location.href);
            urlObj.searchParams.set('from', formData.from);
            urlObj.searchParams.set('to', formData.to);
            urlObj.searchParams.set('sort_column', formData.sort_column);
            urlObj.searchParams.set('sort_direction', formData.sort_direction);
            if (formData.vendor_id) {
                urlObj.searchParams.set('vendor_id', formData.vendor_id);
            } else {
                urlObj.searchParams.delete('vendor_id');
            }
            
            if (formData.payment_status) {
                urlObj.searchParams.set('payment_status', formData.payment_status);
            } else {
                urlObj.searchParams.delete('payment_status');
            }
            if (formData.order_status) {
                urlObj.searchParams.set('order_status', formData.order_status);
            } else {
                urlObj.searchParams.delete('order_status');
            }
            if (formData.channel) {
                urlObj.searchParams.set('channel', formData.channel);
            } else {
                urlObj.searchParams.delete('channel');
            }
            if (formData.search) {
                urlObj.searchParams.set('search', formData.search);
            } else {
                urlObj.searchParams.delete('search');
            }
            urlObj.searchParams.set('page', '1');
            window.history.pushState({}, '', urlObj.toString());
            
            // Update export button URL with current filters
            AdvanceReportCommon.updateExportButtonUrl();
            
            // Make AJAX request
            $.ajax({
                url: url,
                type: 'GET',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    // Destroy existing DataTable before updating content
                    self.destroyDataTable();
                    
                    // Replace content
                    var $contentToReplace = $reportContent.find('#report');
                    
                    if ($contentToReplace.length) {
                        $contentToReplace.replaceWith($(response));
                    } else {
                        var $firstChild = $reportContent.children().not('#report-loading-overlay').first();
                        if ($firstChild.length) {
                            $firstChild.replaceWith($(response));
                        } else {
                            $reportLoadingOverlay.detach();
                            $reportContent.html(response);
                            $reportContent.append($reportLoadingOverlay);
                        }
                    }
                    
                    // Re-get overlay reference after content update
                    $reportLoadingOverlay = $('#report-loading-overlay');
                    
                    // Reinitialize DataTable after content update
                    setTimeout(function() {
                        var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
                        var $mainTable = $('#mainTable');
                        
                        // Apply first column width before initializing DataTable
                        if ($mainTable.length) {
                            AdvanceReportTable.applyFirstColumnWidth($mainTable, reportSlug);
                        }
                        
                        self.initializeDataTable();
                        AdvanceReportShow.initializeStickyHeader();
                        
                        // Update previous search value to match current
                        self.previousSearchValue = searchValue;
                        
                        // Reinitialize search based on report configuration
                        var searchType = AdvanceReportCommon.getSearchConfig(reportSlug);
                        
                        if (searchType === 'jquery') {
                            // Will be initialized by DataTable initialization
                        } else {
                            // Reinitialize AJAX search (will use the updated previousSearchValue)
                            self.initializeAjaxSearch();
                        }
                        
                        // Restore search value
                        $searchInput.val(searchValue);
                        if (searchValue && searchValue.length > 0) {
                            $searchClear.fadeIn(200);
                        } else {
                            $searchClear.fadeOut(200);
                        }
                        
                        // Reapply first column width after DataTable initialization
                        setTimeout(function() {
                            if ($mainTable.length) {
                                AdvanceReportTable.applyFirstColumnWidth($mainTable, reportSlug);
                            }
                        }, 300);
                        
                        // Update export button URL after content update
                        AdvanceReportCommon.updateExportButtonUrl();
                    }, 50);
                    
                    // Hide loading overlay
                    if ($reportLoadingOverlay.length) {
                        $reportLoadingOverlay.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading report data:', error);
                    
                    // Hide loading overlay
                    if ($reportLoadingOverlay.length) {
                        $reportLoadingOverlay.hide();
                    }
                }
            });
        }
        
        // Search on input with debounce
        $searchInput.on('keyup.reportSearch input.reportSearch paste.reportSearch', function() {
            var searchValue = $(this).val().trim();
            
            // Check if value actually changed
            if (searchValue === self.previousSearchValue) {
                return; // No change, skip AJAX request
            }
            
            // Clear existing timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Show/hide clear button immediately
            if (searchValue.length > 0) {
                $searchClear.fadeIn(200);
            } else {
                $searchClear.fadeOut(200);
            }
            
            // Debounce search - wait 500ms after user stops typing
            searchTimeout = setTimeout(function() {
                // Double check if value changed (in case user typed and then deleted)
                var finalValue = $searchInput.val().trim();
                if (finalValue !== self.previousSearchValue) {
                    self.previousSearchValue = finalValue;
                    performAjaxSearch(finalValue);
                }
            }, 500);
        });
        
        // Clear search button
        $searchClear.on('click.reportSearch', function(e) {
            e.preventDefault();
            
            // Check if value actually changed
            if (self.previousSearchValue === '') {
                return; // Already empty, skip AJAX request
            }
            
            $searchInput.val('').focus();
            $searchClear.fadeOut(200);
            
            // Clear timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Update previous value and perform search immediately when clearing
            self.previousSearchValue = '';
            performAjaxSearch('');
        });
        
        // Clear search on Escape key
        $searchInput.on('keydown.reportSearch', function(e) {
            if (e.key === 'Escape') {
                // Check if value actually changed
                if (self.previousSearchValue === '') {
                    return; // Already empty, skip AJAX request
                }
                
                $(this).val('');
                $searchClear.fadeOut(200);
                
                // Clear timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Update previous value and perform search immediately
                self.previousSearchValue = '';
                performAjaxSearch('');
            }
        });
    }
};

