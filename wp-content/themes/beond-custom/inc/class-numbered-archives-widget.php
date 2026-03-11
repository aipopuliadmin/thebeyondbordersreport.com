<?php
/**
 * Numbered Archives Widget
 * 
 * Displays archives with thumbnails
 *
 * @package Beond_Custom
 */

class Beond_Numbered_Archives_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'beond_numbered_archives',
            esc_html__( 'Numbered Archives', 'beond-custom' ),
            array(
                'description' => esc_html__( 'Display archives with thumbnails', 'beond-custom' ),
                'classname'   => 'beond-numbered-archives-widget'
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Archives', 'beond-custom' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 6;
        $type = ! empty( $instance['type'] ) ? $instance['type'] : 'monthly';

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];
        }

        global $wpdb;
        
        // Get archives
        if ( $type === 'monthly' ) {
            $query = "SELECT YEAR(post_date) AS year, MONTH(post_date) AS month, COUNT(ID) AS post_count
                     FROM {$wpdb->posts}
                     WHERE post_type = 'post' AND post_status = 'publish'
                     GROUP BY year, month
                     ORDER BY year DESC, month DESC
                     LIMIT %d";
        } else {
            $query = "SELECT YEAR(post_date) AS year, COUNT(ID) AS post_count
                     FROM {$wpdb->posts}
                     WHERE post_type = 'post' AND post_status = 'publish'
                     GROUP BY year
                     ORDER BY year DESC
                     LIMIT %d";
        }
        
        $archives = $wpdb->get_results( $wpdb->prepare( $query, $number ) );

        if ( ! empty( $archives ) ) {
            echo '<div class="numbered-archives">';
            
            foreach ( $archives as $archive ) {
                if ( $type === 'monthly' ) {
                    $url = get_month_link( $archive->year, $archive->month );
                    $date = new DateTime( $archive->year . '-' . $archive->month . '-01' );
                    $archive_title = $date->format( 'F Y' );
                } else {
                    $url = get_year_link( $archive->year );
                    $archive_title = $archive->year;
                }
                
                // Get a post from this archive for thumbnail
                $post_query = new WP_Query( array(
                    'posts_per_page' => 1,
                    'year'           => $archive->year,
                    'monthnum'       => $type === 'monthly' ? $archive->month : null,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ) );
                
                $thumbnail_id = null;
                if ( $post_query->have_posts() ) {
                    $post_query->the_post();
                    $thumbnail_id = get_post_thumbnail_id();
                    wp_reset_postdata();
                }
                ?>
                <div class="numbered-archive-item">
                    <div class="archive-content">
                        <a href="<?php echo esc_url( $url ); ?>" class="archive-title">
                            <?php echo esc_html( $archive_title ); ?>
                        </a>
                        <span class="archive-count"><?php echo $archive->post_count; ?> articles</span>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Archives', 'beond-custom' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 6;
        $type = ! empty( $instance['type'] ) ? $instance['type'] : 'monthly';
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
                <?php esc_html_e( 'Number of archives to show:', 'beond-custom' ); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" 
                   step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
                <?php esc_html_e( 'Archive Type:', 'beond-custom' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
                <option value="monthly" <?php selected( $type, 'monthly' ); ?>><?php esc_html_e( 'Monthly', 'beond-custom' ); ?></option>
                <option value="yearly" <?php selected( $type, 'yearly' ); ?>><?php esc_html_e( 'Yearly', 'beond-custom' ); ?></option>
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
        $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? absint( $new_instance['number'] ) : 6;
        $instance['type'] = ( ! empty( $new_instance['type'] ) ) ? sanitize_text_field( $new_instance['type'] ) : 'monthly';
        return $instance;
    }
}

/**
 * Register the widget
 */
function beond_register_numbered_archives_widget() {
    register_widget( 'Beond_Numbered_Archives_Widget' );
}
add_action( 'widgets_init', 'beond_register_numbered_archives_widget' );
