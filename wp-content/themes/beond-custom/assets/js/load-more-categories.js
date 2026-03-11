/**
 * Load More Categories
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Handle Load More button click
        $(document).on('click', '.category-load-more-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $container = $button.closest('.beond-numbered-categories-widget').find('.numbered-categories');
            
            // Prevent multiple clicks
            if ($button.attr('data-loading') === 'true') {
                return;
            }
            
            const offset = parseInt($container.attr('data-offset'));
            const perPage = parseInt($container.attr('data-per-page'));
            const orderby = $container.attr('data-orderby');
            
            // Show loading state
            $button.attr('data-loading', 'true');
            $button.find('.load-more-text').hide();
            $button.find('.load-more-loader').show();
            
            // AJAX request
            $.ajax({
                url: beondVars.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'load_more_categories',
                    offset: offset,
                    per_page: perPage,
                    orderby: orderby,
                    nonce: beondVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Append new categories
                        $container.append(response.data.html);
                        
                        // Update offset
                        $container.attr('data-offset', response.data.new_offset);
                        
                        // Hide button if no more categories
                        if (!response.data.has_more) {
                            $button.closest('.category-load-more-wrapper').fadeOut(300);
                        }
                        
                        // Animate new items
                        $container.find('.numbered-category-item:hidden').fadeIn(300);
                    } else {
                        console.error('Error loading categories:', response.data.message);
                        $button.closest('.category-load-more-wrapper').fadeOut(300);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                },
                complete: function() {
                    // Reset loading state
                    $button.attr('data-loading', 'false');
                    $button.find('.load-more-text').show();
                    $button.find('.load-more-loader').hide();
                }
            });
        });
        
    });
    
})(jQuery);
