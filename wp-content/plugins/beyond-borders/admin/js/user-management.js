/**
 * Beyond Borders User Management Admin JavaScript
 * 
 * Handles tab switching, dynamic forms, and user interactions
 */

jQuery(document).ready(function($) {
    'use strict';

    // ===================================================================
    // Tab Switching Functionality
    // ===================================================================
    
    $(document).on('click', '.nav-tab', function(e) {
        e.preventDefault();
        
        var $clicked = $(this);
        var tabId = $clicked.data('tab');
        var $tabContent = $('#tabs-' + tabId);
        
        // Check if tab exists
        if ($tabContent.length === 0) {
            return;
        }
        
        // Remove active class from all nav tabs
        $('.nav-tab').removeClass('nav-tab-active');
        $clicked.addClass('nav-tab-active');
        
        // Hide all tab contents and remove active class
        $('.tab-content').removeClass('active').css({ opacity: 0, display: 'none' });
        
        // Show selected tab: add active class and set visible
        $tabContent.addClass('active').css({ display: 'block', opacity: 1 });
    });

    // ===================================================================
    // Form Handling
    // ===================================================================
    
    // Handle role capabilities form submission
    $(document).on('submit', 'form[action*="beyond-borders"]', function(e) {
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalText = $submitBtn.text();
        
        // Disable button and show loading state
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="spinner"></span> ' + beyondBordersL10n.saving);
        
        // Re-enable after submission
        setTimeout(function() {
            $submitBtn.prop('disabled', false);
            $submitBtn.html(originalText);
        }, 2000);
    });

    // ===================================================================
    // Author Type Selection
    // ===================================================================
    
    // Update author type display on change
    $(document).on('change', 'select[name*="user_author_types"]', function() {
        var $select = $(this);
        var selectedValue = $select.val();
        var $row = $select.closest('tr');
        
        // Add visual feedback
        $row.find('td').css('background-color', '#fffacd');
        
        setTimeout(function() {
            $row.find('td').css('background-color', '');
        }, 500);
    });

    // ===================================================================
    // Capability Checkbox Management
    // ===================================================================
    
    // Highlight changed capabilities
    $(document).on('change', 'input[name*="role_caps"]', function() {
        var $checkbox = $(this);
        var $row = $checkbox.closest('tr');
        
        // Add visual feedback
        $row.css('background-color', '#e8f5e9');
        
        // Fade out highlight
        setTimeout(function() {
            $row.fadeOut(200).fadeIn(200);
            $row.css('background-color', '');
        }, 700);
    });

    // ===================================================================
    // Bulk Capability Assignment
    // ===================================================================
    
    // All capabilities for a role
    $(document).on('click', '.select-all-caps', function(e) {
        e.preventDefault();
        
        var role = $(this).data('role');
        $('input[name*="role_caps[' + role + ']"]').prop('checked', true);
    });

    // Clear all capabilities for a role
    $(document).on('click', '.clear-all-caps', function(e) {
        e.preventDefault();
        
        var role = $(this).data('role');
        $('input[name*="role_caps[' + role + ']"]').prop('checked', false);
    });

    // ===================================================================
    // Search and Filter
    // ===================================================================
    
    // Filter users table by search
    $(document).on('keyup', '#user-search', function() {
        var searchText = $(this).val().toLowerCase();
        
        $('#users-table tbody tr').each(function() {
            var $row = $(this);
            var rowText = $row.text().toLowerCase();
            
            if (rowText.indexOf(searchText) !== -1) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    });

    // ===================================================================
    // Statistics and Info
    // ===================================================================
    
    // Show role details on hover
    $(document).on('mouseenter', 'tr[data-role]', function() {
        var $row = $(this);
        var role = $row.data('role');
        
        // Show tooltip or additional info
        // This can be extended based on requirements
    });

    // ===================================================================
    // User Feedback and Validation
    // ===================================================================
    
    // Prevent form submission if no changes made
    var originalFormState = $('form').serialize();
    
    $(window).on('beforeunload', function() {
        var currentFormState = $('form').serialize();
        
        if (originalFormState !== currentFormState) {
            return true; // Show unsaved changes warning
        }
    });

    // Mark form as unchanged after successful submission
    $(document).on('submit', 'form', function() {
        setTimeout(function() {
            originalFormState = $('form').serialize();
        }, 500);
    });

    // ===================================================================
    // Accessibility Enhancements
    // ===================================================================
    
    // Add ARIA labels for screen readers
    $(document).on('focus', 'input[type="checkbox"]', function() {
        var role = $(this).closest('tr').find('th').first().text();
        $(this).attr('aria-label', 'Toggle capability for ' + role);
    });

    // ===================================================================
    // Export Functionality (for future enhancement)
    // ===================================================================
    
    $(document).on('click', '.export-user-data', function(e) {
        e.preventDefault();
        
        // This would trigger CSV export
        // wp_ajax action: 'export_user_data'
    });

});

// Localization object (passed from PHP)
var beyondBordersL10n = beyondBordersL10n || {
    saving: 'Saving...',
    saved: 'Saved',
    error: 'Error',
};
