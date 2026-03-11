<?php
/**
 * Custom Walker for Mobile Accordion Menu
 *
 * @package Beond_Custom
 */

class Mobile_Accordion_Walker extends Walker_Nav_Menu {
    
    /**
     * Start the element output for parent items.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'mobile-menu-item';
        
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $classes[] = 'has-submenu';
        }
        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        
        $output .= $indent . '<div' . $class_names . '>';
        
        // Item header (link + toggle for items with children)
        $output .= '<div class="mobile-menu-item-header">';
        
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
        $atts['class']  = 'mobile-menu-link';
        
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
        
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
        
        $item_output = '<a' . $attributes . '>' . $title . '</a>';
        
        // Add toggle button for items with children
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $item_output .= '<button class="mobile-submenu-toggle" data-depth="' . $depth . '" aria-label="Toggle submenu">';
            $item_output .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor">';
            $item_output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6l4 4 4-4"></path>';
            $item_output .= '</svg>';
            $item_output .= '</button>';
        }
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        $output .= '</div>'; // .mobile-menu-item-header
    }
    
    /**
     * Start the submenu wrapper.
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<div class=\"mobile-submenu\">\n";
    }
    
    /**
     * End the submenu wrapper.
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</div>\n";
    }
    
    /**
     * End the element output.
     */
    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= "</div>\n";
    }
}
