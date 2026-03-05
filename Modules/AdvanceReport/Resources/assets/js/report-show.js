/**
 * Advance Report Module - Report Show Page
 * 
 * Handles functionality for the report show page (date picker, pagination, form submission, sticky header)
 * 
 * @module AdvanceReport.Show
 * @version 1.0.0
 */

"use strict";

/**
 * Report show page functionality
 */
var AdvanceReportShow = {
    
    /**
     * Initialize date range picker
     */
    initializeDateRangePicker: function() {
        var self = this;
        
        if ($('#daterange-btn').length) {
            // Set default dates if not provided
            var defaultStart = startDate !== 'undefined' ? moment(startDate) : moment().subtract(29, 'days');
            var defaultEnd = endDate !== 'undefined' ? moment(endDate) : moment();
            
            // Update display on initial load
            AdvanceReportCommon.updateDateRangeDisplay(defaultStart, defaultEnd);
            
            // Initialize daterangepicker
            $('#daterange-btn').daterangepicker(daterangeConfig(startDate, endDate), function(start, end, label) {
                // Update hidden inputs
                $('#startfrom').val(start.format('YYYY-MM-DD'));
                $('#endto').val(end.format('YYYY-MM-DD'));
                
                // Update display with YYYY-MM-DD format
                AdvanceReportCommon.updateDateRangeDisplay(start, end);
                // Note: Export button URL will be updated when Apply filter is clicked
            });
        }
    },
    
    /**
     * Handle AJAX pagination for product reports
     */
    bindPagination: function() {
        var self = this;
        
        // Handle pagination link clicks
        $(document).on('click', '.report-pagination-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (!page) return;
            
            self.loadProductReportPage(page);
        });
    },
    
    /**
     * Load product report page via AJAX
     */
    loadProductReportPage: function(page, sortColumn, sortDirection) {
        var self = this;
        var $reportContent = $('#report-content');
        var $reportLoadingOverlay = $('#report-loading-overlay');
        
        // Get current sort parameters if not provided
        if (!sortColumn) {
            var urlParams = new URLSearchParams(window.location.search);
            var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
            var defaultSortColumn = typeof AdvanceReportConfig !== 'undefined' 
                ? AdvanceReportConfig.getDefaultSortColumnName(reportSlug)
                : 'total_sales';
            sortColumn = urlParams.get('sort_column') || defaultSortColumn;
            sortDirection = urlParams.get('sort_direction') || 'desc';
        }
        
        // Get form data
        var formData = {
            from: $('#startfrom').val(),
            to: $('#endto').val(),
            vendor_id: $('#vendor_id').val() || '',
            payment_status: $('#payment_status').val() || '',
            order_status: $('#order_status').val() || '',
            channel: $('#channel').val() || '',
            search: $('#reportTableSearch').val() || '', // Preserve search value
            page: page,
            per_page: 25,
            sort_column: sortColumn,
            sort_direction: sortDirection
        };
        
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
        
        // Get report slug from URL
        var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
        var url = window.location.origin + window.location.pathname;
        
        // Update URL with pagination, filters, and sorting
        var urlObj = new URL(window.location.href);
        urlObj.searchParams.set('from', formData.from);
        urlObj.searchParams.set('to', formData.to);
        urlObj.searchParams.set('page', formData.page);
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
                // Replace content
                var $contentToReplace = $reportContent.find('#report');
                
                if ($contentToReplace.length) {
                    $contentToReplace.replaceWith($(response));
                } else {
                    $reportContent.html(response);
                }
                
                // Re-get overlay reference
                $reportLoadingOverlay = $('#report-loading-overlay');
                
                // Apply first column width after content update
                setTimeout(function() {
                    var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
                    var $mainTable = $('#mainTable');
                    
                    if ($mainTable.length) {
                        AdvanceReportTable.applyFirstColumnWidth($mainTable, reportSlug);
                    }
                }, 100);
                
                // Update export button URL with current filters
                AdvanceReportCommon.updateExportButtonUrl();
                
                // Hide loading overlay
                if ($reportLoadingOverlay.length) {
                    $reportLoadingOverlay.hide();
                }
                
                // Scroll to top of table
                $('html, body').animate({
                    scrollTop: $('#report').offset().top - 200
                }, 300);
            },
            error: function(xhr, status, error) {
                console.error('Error loading report page:', error);
                
                // Hide loading overlay
                if ($reportLoadingOverlay.length) {
                    $reportLoadingOverlay.hide();
                }
            }
        });
    },
    
    /**
     * Initialize table sorting for product report
     */
    initializeTableSorting: function() {
        var self = this;
        
        // Only initialize for reports that use AJAX sorting
        var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
        if (!AdvanceReportConfig.isAjaxSortingEnabled(reportSlug)) {
            return;
        }
        
        // Handle column header clicks
        $(document).on('click', '#mainTable thead th.sortable, #advanceReportTable thead th.sortable', function(e) {
            e.preventDefault();
            
            var $th = $(this);
            var column = $th.data('column');
            var currentSort = $th.data('sort') || '';
            
            // Determine new sort direction
            var newDirection = 'asc';
            if (currentSort === 'asc') {
                newDirection = 'desc';
            } else if (currentSort === 'desc') {
                newDirection = 'asc';
            } else {
                // Default to desc for numeric columns, asc for text columns
                if (['quantity_sold', 'order_count', 'total_sales', 'total_tax', 'avg_unit_price'].indexOf(column) !== -1) {
                    newDirection = 'desc';
                } else {
                    newDirection = 'asc';
                }
            }
            
            // Update export button URL before loading new page
            AdvanceReportCommon.updateExportButtonUrl();
            
            // Load first page with new sorting
            self.loadProductReportPage(1, column, newDirection);
        });
    },
    
    /**
     * Bind form submission handler for AJAX filtering
     */
    bindFormSubmission: function() {
        var self = this;
        
        $('#reportForm').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $applyBtn = $('#applyFilterBtn');
            var $reportContent = $('#report-content');
            var $reportLoadingOverlay = $('#report-loading-overlay');
            
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
                page: 1, // Reset to first page on filter change
                per_page: 25,
                sort_column: sortColumn,
                sort_direction: sortDirection
            };
            
            // Show loading state
            var $btnText = $applyBtn.find('.btn-text');
            var $btnSpinner = $applyBtn.find('.btn-loading-spinner');
            
            $applyBtn.prop('disabled', true);
            if ($btnText.length) {
                $btnText.show();
            }
            if ($btnSpinner.length) {
                $btnSpinner.show();
            }
            
            // Show loading overlay on table
            if ($reportLoadingOverlay.length) {
                $reportLoadingOverlay.show();
            } else {
                // Recreate overlay if it doesn't exist
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
            
            // Update URL without reload
            var url = new URL(window.location.href);
            url.searchParams.set('from', formData.from);
            url.searchParams.set('to', formData.to);
            url.searchParams.set('sort_column', formData.sort_column);
            url.searchParams.set('sort_direction', formData.sort_direction);
            if (formData.vendor_id) {
                url.searchParams.set('vendor_id', formData.vendor_id);
            } else {
                url.searchParams.delete('vendor_id');
            }
            
            if (formData.payment_status) {
                url.searchParams.set('payment_status', formData.payment_status);
            } else {
                url.searchParams.delete('payment_status');
            }
            if (formData.order_status) {
                url.searchParams.set('order_status', formData.order_status);
            } else {
                url.searchParams.delete('order_status');
            }
            if (formData.channel) {
                url.searchParams.set('channel', formData.channel);
            } else {
                url.searchParams.delete('channel');
            }
            if (formData.search) {
                url.searchParams.set('search', formData.search);
            } else {
                url.searchParams.delete('search');
            }
            window.history.pushState({}, '', url.toString());
            
            // Update export button URL with current filters
            AdvanceReportCommon.updateExportButtonUrl();
            
            // Reset previous search value to match current search after filter change
            AdvanceReportTable.previousSearchValue = formData.search || '';
            
            // Make AJAX request
            $.ajax({
                url: $form.attr('action'),
                type: 'GET',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    // Destroy existing DataTable before updating content
                    AdvanceReportTable.destroyDataTable();
                    
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
                        
                        AdvanceReportTable.initializeDataTable();
                        
                        // Reconnect search functionality based on report configuration
                        var searchType = AdvanceReportCommon.getSearchConfig(reportSlug);
                        
                        if (searchType === 'jquery') {
                            // Reconnect jQuery/DataTables search
                            setTimeout(function() {
                                $(document).trigger('searchReconnect.reportSearch');
                            }, 100);
                        }
                        
                        // Reapply first column width after DataTable initialization
                        setTimeout(function() {
                            if ($mainTable.length) {
                                AdvanceReportTable.applyFirstColumnWidth($mainTable, reportSlug);
                            }
                        }, 300);
                        
                        // Update export button URL after content update
                        AdvanceReportCommon.updateExportButtonUrl();
                        
                        // Hide loading overlay after DataTable is initialized
                        setTimeout(function() {
                            if ($reportLoadingOverlay.length) {
                                $reportLoadingOverlay.hide();
                            }
                        }, 50);
                    }, 200);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading report data:', error);
                    
                    // Get column headers from current table or use defaults
                    var $existingTable = $(AdvanceReportCommon.tableConfig.tableId);
                    var columnHeaders = [];
                    
                    if ($existingTable.length && $existingTable.find('thead th').length > 0) {
                        columnHeaders = AdvanceReportCommon.getTableColumnHeaders($existingTable);
                    } else {
                        // Default headers for total-sales-over-time report
                        columnHeaders = ['Date', 'Total Sales', 'Number of Orders', 'Average Order Value'];
                    }
                    
                    var errorMsg = jsLang('Error loading report data. Please try again.');
                    var errorTableHTML = AdvanceReportCommon.buildErrorTableHTML(columnHeaders, errorMsg);
                    
                    // Destroy existing DataTable
                    AdvanceReportTable.destroyDataTable();
                    
                    // Show error message
                    var $contentToReplace = $reportContent.find('#report');
                    if ($contentToReplace.length) {
                        $contentToReplace.replaceWith(errorTableHTML);
                    } else {
                        $reportContent.html(errorTableHTML);
                    }
                    
                    // Re-get overlay reference
                    $reportLoadingOverlay = $('#report-loading-overlay');
                    
                    // Hide loading overlay
                    if ($reportLoadingOverlay.length) {
                        $reportLoadingOverlay.hide();
                    }
                },
                complete: function() {
                    // Always reset button state
                    var $applyBtnRef = $('#applyFilterBtn');
                    var $btnTextRef = $applyBtnRef.find('.btn-text');
                    var $btnSpinnerRef = $applyBtnRef.find('.btn-loading-spinner');
                    
                    $applyBtnRef.prop('disabled', false);
                    if ($btnTextRef.length) {
                        $btnTextRef.show();
                    }
                    if ($btnSpinnerRef.length) {
                        $btnSpinnerRef.hide();
                    }
                    
                    // Ensure overlay is hidden
                    var $overlayRef = $('#report-loading-overlay');
                    if ($overlayRef.length) {
                        $overlayRef.hide();
                    }
                }
            });
        });
    },
    
    /**
     * Initialize sticky header for show page
     * Handles sticky header that appears when scrolling past table
     */
    initializeStickyHeader: function() {
        var self = this;
        
        // Only initialize if sticky header elements exist
        const $tableContainer = $('#tableContainer');
        const $stickyHeader = $('#stickyHeader');
        const $stickyHeaderInner = $('#stickyHeaderInner');
        const $tableWrapper = $('#tableWrapper');
        const $mainTable = $('#mainTable');
        const $stickyTable = $('#advanceReportTable');
        
        if (!$tableContainer.length || !$stickyHeader.length || !$mainTable.length) {
            return;
        }

        let ticking = false;

        // Copy column widths from main table to sticky header
        function syncColumnWidths() {
            const $mainHeaders = $mainTable.find('thead th');
            const $stickyHeaders = $stickyTable.find('thead th');

            $mainHeaders.each(function (index) {
                const width = $(this).outerWidth();
                const $stickyTh = $stickyHeaders.eq(index);
                if ($stickyTh.length) {
                    $stickyTh.css({
                        width: width + 'px',
                        minWidth: width + 'px',
                        maxWidth: width + 'px'
                    });
                }
            });

            // Set table width
            $stickyTable.css('width', $mainTable.outerWidth() + 'px');
        }

        // Sync horizontal scroll
        $tableContainer.on('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    $stickyHeaderInner.scrollLeft($tableContainer.scrollLeft());
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Update sticky header visibility and position
        function updateStickyHeader() {
            const wrapperRect = $tableWrapper[0].getBoundingClientRect();

            // Show sticky header when table header scrolls past top
            if (wrapperRect.top < 0 && wrapperRect.bottom > 60) {
                if (!$stickyHeader.hasClass('visible')) {
                    syncColumnWidths();
                    $stickyHeader.addClass('visible');
                }

                // Position and size the sticky header
                $stickyHeader.css({
                    top: '0px',
                    left: wrapperRect.left + 'px',
                    width: wrapperRect.width + 'px'
                });
            } else {
                $stickyHeader.removeClass('visible');
            }
        }

        // Listen for scroll and resize events
        let scrollTicking = false;

        $(window).on('scroll', function () {
            if (!scrollTicking) {
                window.requestAnimationFrame(function () {
                    updateStickyHeader();
                    scrollTicking = false;
                });
                scrollTicking = true;
            }
        });

        $(window).on('resize', function () {
            syncColumnWidths();
            updateStickyHeader();
        });

        // Initial setup
        $(window).on('load', function () {
            syncColumnWidths();
            updateStickyHeader();
        });
        
        // Also run on document ready with a small delay to ensure table is rendered
        setTimeout(function() {
            syncColumnWidths();
            updateStickyHeader();
        }, 100);
        
            // Initialize sticky header sorting sync if enabled in config
        var reportSlug = AdvanceReportCommon.getCurrentReportSlug();
        if (typeof AdvanceReportConfig !== 'undefined' && AdvanceReportConfig.isStickyHeaderEnabled(reportSlug)) {
            self.initializeStickyHeaderSorting();
        }
    },
    
    /**
     * Bind filter change handlers
     * Note: Export button URL is updated only when Apply filter is clicked, not on individual filter changes
     */
    bindFilterChangeHandlers: function() {
        var self = this;
        // Filter change handlers removed - export button updates only on Apply button click
        // This prevents export URL from updating until filters are actually applied
    },
    
    /**
     * Initialize sticky header sorting synchronization for total-sales-over-time report
     * Syncs clicks on sticky header with main table sorting and visual state
     */
    initializeStickyHeaderSorting: function() {
        var self = this;
        var $advanceTable = $('#advanceReportTable');
        var $mainTable = $('#mainTable');
        
        if (!$advanceTable.length || !$mainTable.length) {
            return;
        }
        
        /**
         * Sync visual state (active-asc/active-desc classes) between sticky header and main table
         */
        function syncSortingState() {
            var $mainHeaders = $mainTable.find('thead th');
            var $advanceHeaders = $advanceTable.find('thead th');
            
            // Remove all active classes from both tables
            $advanceHeaders.removeClass('active-asc active-desc');
            $mainHeaders.removeClass('active-asc active-desc');
            
            // Sync classes based on DataTables sorting state
            $mainHeaders.each(function(index) {
                var $mainTh = $(this);
                var $advanceTh = $advanceHeaders.eq(index);
                
                // Check DataTables classes
                if ($mainTh.hasClass('sorting_asc')) {
                    $advanceTh.addClass('active-asc');
                    $mainTh.addClass('active-asc');
                } else if ($mainTh.hasClass('sorting_desc')) {
                    $advanceTh.addClass('active-desc');
                    $mainTh.addClass('active-desc');
                }
            });
        }
        
        // Wait for DataTable to be initialized
        var checkDataTable = setInterval(function() {
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#mainTable')) {
                clearInterval(checkDataTable);
                
                // Sync click: sticky header -> mainTable
                $advanceTable.find('th.sortable').off('click.stickySort').on('click.stickySort', function(e) {
                    e.preventDefault();
                    var columnIndex = $(this).index();
                    var $mainTh = $mainTable.find('thead th').eq(columnIndex);
                    
                    if ($mainTh.length) {
                        // Trigger click on main table header (DataTables will handle sorting)
                        $mainTh.trigger('click');
                    }
                });
                
                // Sync visual state when DataTables sorting changes
                $mainTable.on('order.dt', function() {
                    // Use setTimeout to ensure DataTables has updated classes
                    setTimeout(function() {
                        syncSortingState();
                    }, 10);
                });
                
                // Initial sync after DataTable is ready
                setTimeout(function() {
                    syncSortingState();
                }, 100);
            }
        }, 100);
        
        // Clear interval after 10 seconds to prevent infinite checking
        setTimeout(function() {
            clearInterval(checkDataTable);
        }, 10000);
    }
};

