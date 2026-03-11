/**
 * Beyond Borders Authentication Scripts
 */

(function ($) {
    'use strict';

    // Form validation on submit
    $(document).ready(function () {
        // Login form
        $('.bb-login-form').on('submit', function (e) {
            var email = $('#email').val().trim();
            var password = $('#password').val().trim();

            if (!email || !password) {
                e.preventDefault();
                showError('Please fill in all fields');
                return false;
            }

            if (!isValidEmail(email)) {
                e.preventDefault();
                showError('Please enter a valid email address');
                return false;
            }

            // Show loading state
            setButtonLoading($('.btn-submit'));
        });

        // Registration form
        $('.bb-register-form').on('submit', function (e) {
            var firstName = $('#first_name').val().trim();
            var lastName = $('#last_name').val().trim();
            var email = $('#email').val().trim();
            var password = $('#password').val().trim();
            var confirmPassword = $('#confirm_password').val().trim();
            var profileImage = $('#profile_image')[0].files[0];

            // Validate required fields
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                e.preventDefault();
                showError('Please fill in all required fields');
                return false;
            }

            // Validate email
            if (!isValidEmail(email)) {
                e.preventDefault();
                showError('Please enter a valid email address');
                return false;
            }

            // Validate password length
            if (password.length < 6) {
                e.preventDefault();
                showError('Password must be at least 6 characters long');
                return false;
            }

            // Validate password match
            if (password !== confirmPassword) {
                e.preventDefault();
                showError('Passwords do not match');
                return false;
            }

            // Validate image file if provided
            if (profileImage) {
                var validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                var maxFileSize = 5 * 1024 * 1024; // 5MB

                if (!validImageTypes.includes(profileImage.type)) {
                    e.preventDefault();
                    showError('Please upload a valid image file (JPEG, PNG, or GIF)');
                    return false;
                }

                if (profileImage.size > maxFileSize) {
                    e.preventDefault();
                    showError('Image file size must be less than 5MB');
                    return false;
                }
            }

            // Show loading state
            setButtonLoading($('.btn-submit'));
        });

        // Profile form
        $('.bb-profile-form').on('submit', function (e) {
            var profileImage = $('#profile_image')[0].files[0];

            // Validate image file if provided
            if (profileImage) {
                var validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                var maxFileSize = 5 * 1024 * 1024; // 5MB

                if (!validImageTypes.includes(profileImage.type)) {
                    e.preventDefault();
                    showError('Please upload a valid image file (JPEG, PNG, or GIF)');
                    return false;
                }

                if (profileImage.size > maxFileSize) {
                    e.preventDefault();
                    showError('Image file size must be less than 5MB');
                    return false;
                }
            }

            // Show loading state
            setButtonLoading($('.btn-submit'));
        });

        // Real-time password match validation
        $('#confirm_password').on('keyup', function () {
            var password = $('#password').val();
            var confirmPassword = $(this).val();

            if (confirmPassword !== '') {
                if (password !== confirmPassword) {
                    $(this).addClass('password-mismatch');
                } else {
                    $(this).removeClass('password-mismatch');
                }
            }
        });

        // Real-time email validation
        $('#email').on('blur', function () {
            var email = $(this).val().trim();

            if (email && !isValidEmail(email)) {
                $(this).addClass('input-error');
            } else {
                $(this).removeClass('input-error');
            }
        });
    });

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Show error message
     */
    function showError(message) {
        var errorHtml =
            '<div class="auth-error">' +
            '<ul><li>' +
            message +
            '</li></ul>' +
            '</div>';

        // Remove existing errors
        $('.auth-error').remove();

        // Insert new error at top
        $('.auth-header').after(errorHtml);

        // Scroll to error
        $('html, body').animate(
            {
                scrollTop: $('.auth-error').offset().top - 100,
            },
            300
        );
    }

    /**
     * Set button loading state
     */
    function setButtonLoading($button) {
        var originalText = $button.text();
        $button.prop('disabled', true);
        $button.text('Processing...');

        // Re-enable after a timeout (in case of slow network)
        setTimeout(function () {
            $button.prop('disabled', false);
            $button.text(originalText);
        }, 30000);
    }

    /**
     * Handle drag and drop for file uploads
     */
    function setupDragDrop() {
        var $fileInput = $('#profile_image');
        if ($fileInput.length === 0) {
            return;
        }

        var $dropZone = $fileInput.parent();

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
            $dropZone.on(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(function (eventName) {
            $dropZone.on(eventName, function () {
                $dropZone.addClass('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            $dropZone.on(eventName, function () {
                $dropZone.removeClass('drag-over');
            });
        });

        // Handle dropped files
        $dropZone.on('drop', function (e) {
            var dt = e.originalEvent.dataTransfer;
            var files = dt.files;

            if (files.length > 0) {
                $fileInput.prop('files', files);
            }
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
    }

    // Initialize drag drop on page load
    $(document).ready(function () {
        setupDragDrop();
    });
})(jQuery);
