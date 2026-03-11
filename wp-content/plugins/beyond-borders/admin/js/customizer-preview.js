/**
 * Customizer Preview JavaScript
 * 
 * Updates CSS variables in real-time as colors change in the customizer.
 */

(function($) {
    'use strict';

    // Navy Deep
    wp.customize('beyond_borders_navy_deep', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--navy-deep', newval);
        });
    });

    // Navy Primary
    wp.customize('beyond_borders_navy_primary', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--navy-primary', newval);
        });
    });

    // Gold Primary
    wp.customize('beyond_borders_gold_primary', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--gold-primary', newval);
        });
    });

    // Gold Champagne
    wp.customize('beyond_borders_gold_champagne', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--gold-champagne', newval);
        });
    });

    // Cream
    wp.customize('beyond_borders_cream', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--cream', newval);
        });
    });

    // Charcoal Dark
    wp.customize('beyond_borders_charcoal_dark', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--charcoal-dark', newval);
        });
    });

})(jQuery);
