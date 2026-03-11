(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Font source toggle
        $('.bb-font-source').on('change', function() {
            var element = $(this).data('element');
            var source = $(this).val();
            
            // Hide all options for this element
            $('.bb-font-option-' + element).hide();
            
            // Show relevant options
            if (source === 'google') {
                $('.bb-font-option-' + element + '.bb-google-font-row').show();
            } else if (source === 'custom') {
                $('.bb-font-option-' + element + '.bb-custom-font-row').show();
            }
            
            updatePreview(element);
        });
        
        // Google font selection change
        $('.bb-google-font-select').on('change', function() {
            var element = $(this).closest('.bb-font-section').find('.bb-font-source:checked').data('element');
            updatePreview(element);
        });
        
        // Font weight change
        $('select[name$="_weight"]').on('change', function() {
            var name = $(this).attr('name');
            var element = name.replace('_weight', '');
            updatePreview(element);
        });
        
        // Custom font upload
        var fontUploader;
        
        $('.bb-upload-font-btn').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var element = button.data('element');
            
            // If the uploader object has already been created, reopen the dialog
            if (fontUploader) {
                fontUploader.open();
                return;
            }
            
            // Create the media frame
            fontUploader = wp.media({
                title: 'Select Font Files',
                button: {
                    text: 'Use Selected Fonts'
                },
                multiple: true,
                library: {
                    type: ['font/woff', 'font/woff2', 'application/x-font-ttf', 'font/ttf']
                }
            });
            
            // When files are selected
            fontUploader.on('select', function() {
                var selection = fontUploader.state().get('selection');
                var fileIds = [];
                var previewHtml = '';
                
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    fileIds.push(attachment.id);
                    
                    previewHtml += '<div class="bb-font-file">' +
                        '<span class="dashicons dashicons-media-document"></span>' +
                        '<a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>' +
                        '</div>';
                });
                
                // Update hidden input
                $('#' + element + '_custom_files').val(fileIds.join(','));
                
                // Update preview
                $('#' + element + '_font_preview').html(previewHtml);
                
                updatePreview(element);
            });
            
            // Open the uploader
            fontUploader.open();
        });
        
        // Update font preview
        function updatePreview(element) {
            var source = $('input[name="' + element + '_source"]:checked').val();
            var weight = $('select[name="' + element + '_weight"]').val();
            var previewElement = $('#preview_' + element + ' .bb-preview-text');
            
            if (source === 'google') {
                var fontFamily = $('select[name="' + element + '_google_font"]').val();
                
                // Load Google Font dynamically
                loadGoogleFont(fontFamily, weight);
                
                // Apply font to preview
                previewElement.css({
                    'font-family': fontFamily + ', serif',
                    'font-weight': weight
                });
                
            } else if (source === 'custom') {
                var customName = $('input[name="' + element + '_custom_name"]').val();
                
                if (customName) {
                    previewElement.css({
                        'font-family': customName + ', sans-serif',
                        'font-weight': weight
                    });
                }
                
            } else if (source === 'system') {
                previewElement.css({
                    'font-family': '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    'font-weight': weight
                });
            }
        }
        
        // Load Google Font dynamically
        var loadedFonts = [];
        
        function loadGoogleFont(fontFamily, weight) {
            var fontKey = fontFamily + ':' + weight;
            
            // Check if already loaded
            if (loadedFonts.indexOf(fontKey) !== -1) {
                return;
            }
            
            // Create link element
            var link = document.createElement('link');
            link.href = 'https://fonts.googleapis.com/css2?family=' + 
                        fontFamily.replace(/ /g, '+') + ':wght@' + weight + '&display=swap';
            link.rel = 'stylesheet';
            
            document.head.appendChild(link);
            loadedFonts.push(fontKey);
        }
        
        // Initialize previews on page load
        $('.bb-font-section').each(function() {
            var element = $(this).find('.bb-font-source:checked').data('element');
            if (element) {
                updatePreview(element);
            }
        });
        
        // Font combination presets (future feature)
        window.applyFontPreset = function(preset) {
            // Could add preset combinations like:
            // - Classic: Playfair Display + Lora
            // - Modern: Inter + Poppins
            // - Editorial: Crimson Text + Libre Baskerville
            console.log('Applying preset: ' + preset);
        };
        
    });

})(jQuery);
