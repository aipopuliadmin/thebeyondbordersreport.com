<?php
/**
 * Numbered Recent Posts Widget
 * 
 * Displays recent posts with numbered badges and thumbnails
 *
 * @package Beond_Custom
 */

class Beond_Numbered_Recent_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'beond_numbered_recent_posts',
            esc_html__( 'Numbered Recent Posts', 'beond-custom' ),
            array(
                'description' => esc_html__( 'Display recent posts with numbered badges and thumbnails', 'beond-custom' ),
                'classname'   => 'beond-numbered-recent-posts-widget'
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Posts', 'beond-custom' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];
        }

        $recent_posts = new WP_Query( array(
            'posts_per_page'      => $number,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ) );

        if ( $recent_posts->have_posts() ) {
            echo '<div class="numbered-recent-posts">';
            
            $counter = 1;
            while ( $recent_posts->have_posts() ) {
                $recent_posts->the_post();
                ?>
                <div class="numbered-post-item">
                    <div class="post-thumbnail">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'thumbnail' );
                        } else {
                            echo '<img src="https://picsum.photos/120/120?random=' . get_the_ID() . '" alt="' . esc_attr( get_the_title() ) . '">';
                        }
                        ?>
                        <span class="post-number"><?php echo $counter; ?></span>
                    </div>
                    <div class="post-content">
                        <a href="<?php the_permalink(); ?>" class="post-title">
                            <?php the_title(); ?>
                        </a>
                    </div>
                </div>
                <?php
                $counter++;
            }
            
            echo '</div>';
            wp_reset_postdata();
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Posts', 'beond-custom' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
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
                <?php esc_html_e( 'Number of posts to show:', 'beond-custom' ); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" 
                   step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3">
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
        return $instance;
    }
}

/**
 * Register the widget
 */
function beond_register_numbered_recent_posts_widget() {
    register_widget( 'Beond_Numbered_Recent_Posts_Widget' );
}
add_action( 'widgets_init', 'beond_register_numbered_recent_posts_widget' );
