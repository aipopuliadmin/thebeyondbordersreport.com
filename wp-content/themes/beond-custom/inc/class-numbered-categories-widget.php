<?php
/**
 * Numbered Categories Widget
 * 
 * Displays categories with numbered badges and thumbnails
 *
 * @package Beond_Custom
 */

class Beond_Numbered_Categories_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'beond_numbered_categories',
            esc_html__( 'Numbered Categories', 'beond-custom' ),
            array(
                'description' => esc_html__( 'Display categories with numbered badges and thumbnails', 'beond-custom' ),
                'classname'   => 'beond-numbered-categories-widget'
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Categories', 'beond-custom' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'count';
        $show_load_more = ! empty( $instance['show_load_more'] ) ? $instance['show_load_more'] : 'yes';

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];
        }

        $categories = get_categories( array(
            'orderby'    => $orderby,
            'order'      => 'DESC',
            'number'     => $number,
            'hide_empty' => true,
        ) );

        $total_categories = wp_count_terms( 'category', array( 'hide_empty' => true ) );

        if ( ! empty( $categories ) ) {
            echo '<div class="numbered-categories" data-orderby="' . esc_attr( $orderby ) . '" data-offset="' . esc_attr( $number ) . '" data-per-page="' . esc_attr( $number ) . '">';
            
            $counter = 1;
            foreach ( $categories as $category ) {
                $this->render_category_item( $category, $counter );
                $counter++;
            }
            
            echo '</div>';
            
            // Show Load More button if enabled and there are more categories
            if ( $show_load_more === 'yes' && $total_categories > $number ) {
                echo '<div class="category-load-more-wrapper">';
                echo '<button class="category-load-more-btn" data-loading="false">';
                echo '<span class="load-more-text">' . esc_html__( 'Load More Categories', 'beond-custom' ) . '</span>';
                echo '<span class="load-more-loader" style="display:none;">' . esc_html__( 'Loading...', 'beond-custom' ) . '</span>';
                echo '</button>';
                echo '</div>';
            }
        }

        echo $args['after_widget'];
    }

    /**
     * Render a single category item
     */
    public function render_category_item( $category, $counter ) {
        $category_link = get_category_link( $category->term_id );
        $category_image_id = get_term_meta( $category->term_id, 'category_featured_image', true );
        
        if ( ! $category_image_id ) {
            $category_image_id = get_term_meta( $category->term_id, 'category_image_id', true );
        }
        
        if ( ! $category_image_id ) {
            $category_image_id = get_term_meta( $category->term_id, 'featured_image', true );
        }
        ?>
        <div class="numbered-category-item">
            <div class="category-thumbnail">
                <?php 
                if ( $category_image_id ) {
                    echo wp_get_attachment_image( $category_image_id, 'thumbnail', false, array( 'alt' => esc_attr( $category->name ) ) );
                } else {
                    echo '<img src="https://picsum.photos/120/120?random=' . $category->term_id . '" alt="' . esc_attr( $category->name ) . '">';
                }
                ?>
            </div>
            <div class="category-content">
                <a href="<?php echo esc_url( $category_link ); ?>" class="category-title">
                    <?php echo esc_html( $category->name ); ?>
                </a>
                <span class="category-count"><?php echo $category->count; ?> articles</span>
            </div>
        </div>
        <?php
    }

    /**
     * Back-end widget form
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Categories', 'beond-custom' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'count';
        $show_load_more = ! empty( $instance['show_load_more'] ) ? $instance['show_load_more'] : 'yes';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Title:', 'beond-custom' ); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" 
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
                <?php esc_html_e( 'Number of categories to show:', 'beond-custom' ); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" 
                   step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
                <?php esc_html_e( 'Order by:', 'beond-custom' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
                <option value="count" <?php selected( $orderby, 'count' ); ?>><?php esc_html_e( 'Post Count', 'beond-custom' ); ?></option>
                <option value="name" <?php selected( $orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'beond-custom' ); ?></option>
                <option value="id" <?php selected( $orderby, 'id' ); ?>><?php esc_html_e( 'ID', 'beond-custom' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_load_more' ) ); ?>">
                <?php esc_html_e( 'Show Load More Button:', 'beond-custom' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_load_more' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'show_load_more' ) ); ?>">
                <option value="yes" <?php selected( $show_load_more, 'yes' ); ?>><?php esc_html_e( 'Yes', 'beond-custom' ); ?></option>
                <option value="no" <?php selected( $show_load_more, 'no' ); ?>><?php esc_html_e( 'No', 'beond-custom' ); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? absint( $new_instance['number'] ) : 5;
        $instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? sanitize_text_field( $new_instance['orderby'] ) : 'count';
        $instance['show_load_more'] = ( ! empty( $new_instance['show_load_more'] ) ) ? sanitize_text_field( $new_instance['show_load_more'] ) : 'yes';
        return $instance;
    }
}

/**
 * Register the widget
 */
function beond_register_numbered_categories_widget() {
    register_widget( 'Beond_Numbered_Categories_Widget' );
}
add_action( 'widgets_init', 'beond_register_numbered_categories_widget' );

/**
 * AJAX handler for loading more categories
 */
function beond_load_more_categories() {
    check_ajax_referer( 'beond-nonce', 'nonce' );
    
    $offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
    $per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 5;
    $orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'count';
    
    $categories = get_categories( array(
        'orderby'    => $orderby,
        'order'      => 'DESC',
        'number'     => $per_page,
        'offset'     => $offset,
        'hide_empty' => true,
    ) );
    
    $total_categories = wp_count_terms( 'category', array( 'hide_empty' => true ) );
    $has_more = ( $offset + $per_page ) < $total_categories;
    
    if ( ! empty( $categories ) ) {
        $widget = new Beond_Numbered_Categories_Widget();
        ob_start();
        
        $counter = $offset + 1;
        foreach ( $categories as $category ) {
            $widget->render_category_item( $category, $counter );
            $counter++;
        }
        
        $html = ob_get_clean();
        
        wp_send_json_success( array(
            'html' => $html,
            'has_more' => $has_more,
            'new_offset' => $offset + $per_page
        ) );
    } else {
        wp_send_json_error( array( 'message' => 'No more categories found.' ) );
    }
}
add_action( 'wp_ajax_load_more_categories', 'beond_load_more_categories' );
add_action( 'wp_ajax_nopriv_load_more_categories', 'beond_load_more_categories' );

