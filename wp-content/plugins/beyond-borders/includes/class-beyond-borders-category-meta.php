<?php
/**
 * Category Meta Fields
 *
 * Adds custom meta fields to WordPress categories for icons, colors, and images.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Category_Meta {

    /**
     * Initialize the class.
     */
    public function __construct() {
        add_action( 'category_add_form_fields', array( $this, 'add_category_meta_fields' ) );
        add_action( 'category_edit_form_fields', array( $this, 'edit_category_meta_fields' ) );
        add_action( 'created_category', array( $this, 'save_category_meta' ) );
        add_action( 'edited_category', array( $this, 'save_category_meta' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue scripts for category admin.
     */
    public function enqueue_scripts( $hook ) {
        if ( $hook === 'term.php' || $hook === 'edit-tags.php' ) {
            wp_enqueue_media();
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
        }
    }

    /**
     * Add meta fields to category add form.
     */
    public function add_category_meta_fields() {
        ?>
        <div class="form-field term-meta-wrap">
            <h2><?php _e( 'Category Showcase Settings', 'beyond-borders' ); ?></h2>
            <p><?php _e( 'Configure how this category appears in the homepage category showcase.', 'beyond-borders' ); ?></p>
        </div>

        <div class="form-field">
            <label for="category_icon"><?php _e( 'Icon Class or Emoji', 'beyond-borders' ); ?></label>
            <input type="text" name="category_icon" id="category_icon" value="" placeholder="📊 or fas fa-chart-line" />
            <p class="description"><?php _e( 'Enter an emoji or Font Awesome icon class (e.g., fas fa-chart-line)', 'beyond-borders' ); ?></p>
        </div>

        <div class="form-field">
            <label for="category_color"><?php _e( 'Card Background Color', 'beyond-borders' ); ?></label>
            <input type="text" name="category_color" id="category_color" class="category-color-picker" value="#FAF8F5" />
            <p class="description"><?php _e( 'Background color for the category card on homepage', 'beyond-borders' ); ?></p>
        </div>

        <div class="form-field">
            <label for="category_text_color"><?php _e( 'Text Color', 'beyond-borders' ); ?></label>
            <input type="text" name="category_text_color" id="category_text_color" class="category-color-picker" value="#0A1628" />
            <p class="description"><?php _e( 'Text color for the category card', 'beyond-borders' ); ?></p>
        </div>

        <div class="form-field">
            <label for="category_image"><?php _e( 'Featured Image', 'beyond-borders' ); ?></label>
            <div class="category-image-wrapper">
                <img src="" class="category-image-preview" style="max-width: 200px; display: none;" />
                <input type="hidden" name="category_image" id="category_image" value="" />
                <button type="button" class="button category-image-upload"><?php _e( 'Upload Image', 'beyond-borders' ); ?></button>
                <button type="button" class="button category-image-remove" style="display: none;"><?php _e( 'Remove Image', 'beyond-borders' ); ?></button>
            </div>
            <p class="description"><?php _e( 'Optional image for category showcase', 'beyond-borders' ); ?></p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('.category-color-picker').wpColorPicker();

            // Media uploader for category image
            var mediaUploader;
            $('.category-image-upload').on('click', function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: '<?php _e( 'Choose Category Image', 'beyond-borders' ); ?>',
                    button: {
                        text: '<?php _e( 'Use this image', 'beyond-borders' ); ?>'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#category_image').val(attachment.id);
                    $('.category-image-preview').attr('src', attachment.url).show();
                    $('.category-image-remove').show();
                });
                mediaUploader.open();
            });

            $('.category-image-remove').on('click', function(e) {
                e.preventDefault();
                $('#category_image').val('');
                $('.category-image-preview').attr('src', '').hide();
                $(this).hide();
            });
        });
        </script>
        <?php
    }

    /**
     * Edit meta fields on category edit form.
     */
    public function edit_category_meta_fields( $term ) {
        $icon = get_term_meta( $term->term_id, 'category_icon', true );
        $color = get_term_meta( $term->term_id, 'category_color', true );
        $text_color = get_term_meta( $term->term_id, 'category_text_color', true );
        $image_id = get_term_meta( $term->term_id, 'category_image', true );
        $image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
        ?>
        <tr class="form-field">
            <th scope="row" colspan="2">
                <h2><?php _e( 'Category Showcase Settings', 'beyond-borders' ); ?></h2>
                <p class="description"><?php _e( 'Configure how this category appears in the homepage category showcase.', 'beyond-borders' ); ?></p>
            </th>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="category_icon"><?php _e( 'Icon Class or Emoji', 'beyond-borders' ); ?></label>
            </th>
            <td>
                <input type="text" name="category_icon" id="category_icon" value="<?php echo esc_attr( $icon ); ?>" placeholder="📊 or fas fa-chart-line" style="width: 100%; max-width: 400px;" />
                <p class="description"><?php _e( 'Enter an emoji or Font Awesome icon class (e.g., fas fa-chart-line)', 'beyond-borders' ); ?></p>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="category_color"><?php _e( 'Card Background Color', 'beyond-borders' ); ?></label>
            </th>
            <td>
                <input type="text" name="category_color" id="category_color" class="category-color-picker" value="<?php echo esc_attr( $color ? $color : '#FAF8F5' ); ?>" />
                <p class="description"><?php _e( 'Background color for the category card on homepage', 'beyond-borders' ); ?></p>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="category_text_color"><?php _e( 'Text Color', 'beyond-borders' ); ?></label>
            </th>
            <td>
                <input type="text" name="category_text_color" id="category_text_color" class="category-color-picker" value="<?php echo esc_attr( $text_color ? $text_color : '#0A1628' ); ?>" />
                <p class="description"><?php _e( 'Text color for the category card', 'beyond-borders' ); ?></p>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="category_image"><?php _e( 'Featured Image', 'beyond-borders' ); ?></label>
            </th>
            <td>
                <div class="category-image-wrapper">
                    <?php if ( $image_url ) : ?>
                        <img src="<?php echo esc_url( $image_url ); ?>" class="category-image-preview" style="max-width: 200px; display: block; margin-bottom: 10px;" />
                    <?php else : ?>
                        <img src="" class="category-image-preview" style="max-width: 200px; display: none; margin-bottom: 10px;" />
                    <?php endif; ?>
                    <input type="hidden" name="category_image" id="category_image" value="<?php echo esc_attr( $image_id ); ?>" />
                    <button type="button" class="button category-image-upload"><?php _e( 'Upload Image', 'beyond-borders' ); ?></button>
                    <button type="button" class="button category-image-remove" style="<?php echo $image_url ? '' : 'display: none;'; ?>"><?php _e( 'Remove Image', 'beyond-borders' ); ?></button>
                </div>
                <p class="description"><?php _e( 'Optional image for category showcase', 'beyond-borders' ); ?></p>
            </td>
        </tr>

        <script>
        jQuery(document).ready(function($) {
            $('.category-color-picker').wpColorPicker();

            // Media uploader for category image
            var mediaUploader;
            $('.category-image-upload').on('click', function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: '<?php _e( 'Choose Category Image', 'beyond-borders' ); ?>',
                    button: {
                        text: '<?php _e( 'Use this image', 'beyond-borders' ); ?>'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#category_image').val(attachment.id);
                    $('.category-image-preview').attr('src', attachment.url).show();
                    $('.category-image-remove').show();
                });
                mediaUploader.open();
            });

            $('.category-image-remove').on('click', function(e) {
                e.preventDefault();
                $('#category_image').val('');
                $('.category-image-preview').attr('src', '').hide();
                $(this).hide();
            });
        });
        </script>
        <?php
    }

    /**
     * Save category meta fields.
     */
    public function save_category_meta( $term_id ) {
        if ( isset( $_POST['category_icon'] ) ) {
            update_term_meta( $term_id, 'category_icon', sanitize_text_field( $_POST['category_icon'] ) );
        }
        if ( isset( $_POST['category_color'] ) ) {
            update_term_meta( $term_id, 'category_color', sanitize_hex_color( $_POST['category_color'] ) );
        }
        if ( isset( $_POST['category_text_color'] ) ) {
            update_term_meta( $term_id, 'category_text_color', sanitize_hex_color( $_POST['category_text_color'] ) );
        }
        if ( isset( $_POST['category_image'] ) ) {
            update_term_meta( $term_id, 'category_image', absint( $_POST['category_image'] ) );
        }
    }
}

// Initialize the class
new Beyond_Borders_Category_Meta();
