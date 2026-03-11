/**
 * Newsletter subscription handling
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle newsletter form submission
        $('#newsletter-form-modern').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $button = $form.find('button[type="submit"]');
            var $email = $form.find('#newsletter-email');
            var $message = $('#newsletter-message');
            
            // Disable button and show loading
            $button.prop('disabled', true).text('Subscribing...');
            $message.hide();
            
            // AJAX request
            $.ajax({
                url: beyondBordersNewsletter.ajax_url,
                type: 'POST',
                data: {
                    action: 'subscribe_newsletter',
                    nonce: beyondBordersNewsletter.nonce,
                    email: $email.val()
                },
                success: function(response) {
                    if (response.success) {
                        $message.html('<div style="color: #10b981; font-size: 14px; text-align: center;">' + response.data.message + '</div>').fadeIn();
                        $form[0].reset();
                    } else {
                        $message.html('<div style="color: #ef4444; font-size: 14px; text-align: center;">' + response.data.message + '</div>').fadeIn();
                    }
                },
                error: function() {
                    $message.html('<div style="color: #ef4444; font-size: 14px; text-align: center;">An error occurred. Please try again.</div>').fadeIn();
                },
                complete: function() {
                    $button.prop('disabled', false).text('Subscribe');
                }
            });
        });
    });
})(jQuery);
