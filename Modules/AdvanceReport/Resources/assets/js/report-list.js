/**
 * Advance Report Module - Report List Page
 * 
 * Handles functionality for the index page (report list)
 * 
 * @module AdvanceReport.List
 * @version 1.0.0
 */

"use strict";

/**
 * Report list page functionality
 */
var AdvanceReportList = {
    
    /**
     * Initialize category dropdown for index page
     * Handles multi-select category filtering
     */
    initializeCategoryDropdown: function() {
        // Only initialize if on index page (category dropdown exists)
        var dropdown = $('.custom-category-dropdown');
        if (!dropdown.length) {
            return;
        }
        
        const trigger = dropdown.find('.custom-dropdown-trigger');
        const menu = dropdown.find('.custom-dropdown-menu');
        const options = dropdown.find('.custom-dropdown-option');
        const selectedText = dropdown.find('.custom-dropdown-selected');
        const searchInput = dropdown.find('.custom-dropdown-search-input');
        const checkboxes = dropdown.find('.category-checkbox');
        const btnApply = dropdown.find('.btn-apply-categories');
        const btnClear = dropdown.find('.btn-clear-categories');

        // Toggle dropdown
        trigger.on('click', function(e) {
            e.stopPropagation();
            dropdown.toggleClass('active');
            
            if (dropdown.hasClass('active')) {
                searchInput.focus();
            }
        });

        // Update checkmark on checkbox change
        checkboxes.on('change', function() {
            const option = $(this).closest('.custom-dropdown-option');
            const optionCheck = option.find('.option-check');
            
            if ($(this).is(':checked')) {
                if (optionCheck.length === 0) {
                    option.append('<i class="fa fa-check option-check"></i>');
                }
            } else {
                optionCheck.remove();
            }
        });

        // Prevent closing dropdown when clicking on checkbox
        options.on('click', function(e) {
            if ($(e.target).is('.category-checkbox')) {
                e.stopPropagation();
            }
        });

        // Apply categories filter
        btnApply.on('click', function (e) {
            e.stopPropagation();

            const selectedCategories = [];

            checkboxes.each(function () {
                if ($(this).is(':checked')) {
                    selectedCategories.push($(this).val());
                }
            });

            // Create new URL object
            const url = new URL(window.location.href);

            // Collect keys to remove first (avoid mutating during iteration)
            const keysToRemove = [];
            url.searchParams.forEach((value, key) => {
                if (key.startsWith('categories') || key === 'category') {
                    keysToRemove.push(key);
                }
            });

            // Remove collected keys
            keysToRemove.forEach(key => {
                url.searchParams.delete(key);
            });

            // Add selected categories fresh
            selectedCategories.forEach(cat => {
                url.searchParams.append('categories[]', cat);
            });

            // Preserve 'search' parameter if exists
            const searchParam = new URLSearchParams(window.location.search).get('search');
            if (searchParam) {
                url.searchParams.set('search', searchParam);
            }

            // Redirect to new URL
            window.location.href = url.toString();
        });

        // Clear all categories
        btnClear.on('click', function(e) {
            e.stopPropagation();
            checkboxes.prop('checked', false);
            options.find('.option-check').remove();
        });

        // Search functionality
        searchInput.on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            options.each(function() {
                const optionText = $(this).find('.option-text').text().toLowerCase();
                if (optionText.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!dropdown.is(e.target) && dropdown.has(e.target).length === 0) {
                dropdown.removeClass('active');
                searchInput.val('');
                options.show();
            }
        });

        // Close dropdown on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && dropdown.hasClass('active')) {
                dropdown.removeClass('active');
                searchInput.val('');
                options.show();
            }
        });
    }
};

