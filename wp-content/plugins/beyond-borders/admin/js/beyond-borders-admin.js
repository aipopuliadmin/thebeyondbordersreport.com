/**
 * Admin JavaScript for Beyond Borders plugin.
 *
 * @package Beyond_Borders
 */

(function( $ ) {
    'use strict';

    $(document).ready(function() {
        // Initialize color picker
        if ( typeof $.fn.wpColorPicker !== 'undefined' ) {
            $('.color-picker').wpColorPicker();
        }

        // Admin notices
        setTimeout(function() {
            $('.notice.is-dismissible').fadeOut();
        }, 3000);
    });

})( jQuery );
