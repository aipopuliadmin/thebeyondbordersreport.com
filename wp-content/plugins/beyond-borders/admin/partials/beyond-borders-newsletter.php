<?php
/**
 * Newsletter admin page
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

$current_page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$per_page = 50;
$offset = ( $current_page - 1 ) * $per_page;

$subscribers = Beyond_Borders_Newsletter::get_subscribers( 'all', $per_page, $offset );
$total_count = Beyond_Borders_Newsletter::get_subscriber_count( 'all' );
$subscribed_count = Beyond_Borders_Newsletter::get_subscriber_count( 'subscribed' );
$total_pages = ceil( $total_count / $per_page );
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <?php if ( isset( $_GET['deleted'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Subscriber deleted successfully.', 'beyond-borders' ); ?></p>
        </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['error'] ) ) : ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php 
                if ( $_GET['error'] === 'no_subscribers' ) {
                    esc_html_e( 'No subscribers to export.', 'beyond-borders' );
                } else {
                    esc_html_e( 'An error occurred.', 'beyond-borders' );
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="newsletter-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="stat-card" style="background: #fff; padding: 20px; border-left: 4px solid #D4AF37; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;"><?php esc_html_e( 'Total Subscribers', 'beyond-borders' ); ?></h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #D4AF37;"><?php echo number_format( $total_count ); ?></p>
        </div>
        <div class="stat-card" style="background: #fff; padding: 20px; border-left: 4px solid #10b981; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;"><?php esc_html_e( 'Active Subscribers', 'beyond-borders' ); ?></h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #10b981;"><?php echo number_format( $subscribed_count ); ?></p>
        </div>
    </div>

    <div class="newsletter-actions" style="margin: 20px 0; display: flex; gap: 10px; align-items: center;">
        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" style="margin: 0;">
            <input type="hidden" name="action" value="export_newsletter_subscribers">
            <?php wp_nonce_field( 'export_newsletter_subscribers' ); ?>
            <button type="submit" class="button button-primary">
                <span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
                <?php esc_html_e( 'Export to CSV', 'beyond-borders' ); ?>
            </button>
        </form>
        <span class="description"><?php echo sprintf( esc_html__( 'Total: %d subscribers', 'beyond-borders' ), $total_count ); ?></span>
    </div>

    <?php if ( ! empty( $subscribers ) ) : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 50px;"><?php esc_html_e( 'ID', 'beyond-borders' ); ?></th>
                    <th><?php esc_html_e( 'Email', 'beyond-borders' ); ?></th>
                    <th><?php esc_html_e( 'Name', 'beyond-borders' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'beyond-borders' ); ?></th>
                    <th><?php esc_html_e( 'Subscribed Date', 'beyond-borders' ); ?></th>
                    <th><?php esc_html_e( 'IP Address', 'beyond-borders' ); ?></th>
                    <th style="width: 100px;"><?php esc_html_e( 'Actions', 'beyond-borders' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $subscribers as $subscriber ) : ?>
                    <tr>
                        <td><?php echo esc_html( $subscriber->id ); ?></td>
                        <td><strong><?php echo esc_html( $subscriber->email ); ?></strong></td>
                        <td><?php echo esc_html( $subscriber->name ?: '—' ); ?></td>
                        <td>
                            <?php if ( $subscriber->status === 'subscribed' ) : ?>
                                <span class="status-badge" style="display: inline-block; padding: 4px 8px; background: #10b981; color: #fff; border-radius: 3px; font-size: 12px;">
                                    <?php esc_html_e( 'Active', 'beyond-borders' ); ?>
                                </span>
                            <?php else : ?>
                                <span class="status-badge" style="display: inline-block; padding: 4px 8px; background: #6b7280; color: #fff; border-radius: 3px; font-size: 12px;">
                                    <?php echo esc_html( ucfirst( $subscriber->status ) ); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $subscriber->subscribed_date ) ) ); ?></td>
                        <td><?php echo esc_html( $subscriber->ip_address ?: '—' ); ?></td>
                        <td>
                            <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" style="margin: 0;" onsubmit="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this subscriber?', 'beyond-borders' ); ?>');">
                                <input type="hidden" name="action" value="delete_newsletter_subscriber">
                                <input type="hidden" name="subscriber_id" value="<?php echo esc_attr( $subscriber->id ); ?>">
                                <?php wp_nonce_field( 'delete_newsletter_subscriber' ); ?>
                                <button type="submit" class="button button-small button-link-delete" style="color: #b32d2e;">
                                    <?php esc_html_e( 'Delete', 'beyond-borders' ); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $total_pages > 1 ) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links( array(
                        'base' => add_query_arg( 'paged', '%#%' ),
                        'format' => '',
                        'prev_text' => __( '&laquo; Previous', 'beyond-borders' ),
                        'next_text' => __( 'Next &raquo;', 'beyond-borders' ),
                        'total' => $total_pages,
                        'current' => $current_page
                    ) );
                    ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="notice notice-info" style="margin-top: 20px;">
            <p><?php esc_html_e( 'No newsletter subscribers yet. Start collecting subscribers through your website footer!', 'beyond-borders' ); ?></p>
        </div>
    <?php endif; ?>

    <div class="newsletter-info" style="margin-top: 40px; padding: 20px; background: #f9fafb; border-left: 4px solid #D4AF37;">
        <h2><?php esc_html_e( 'How to Use', 'beyond-borders' ); ?></h2>
        <ul style="list-style: disc; margin-left: 20px;">
            <li><?php esc_html_e( 'The newsletter signup form is automatically displayed in your website footer.', 'beyond-borders' ); ?></li>
            <li><?php esc_html_e( 'Enable or disable it from Beyond Borders → Footer Settings.', 'beyond-borders' ); ?></li>
            <li><?php esc_html_e( 'Subscribers are automatically added to this list when they submit the form.', 'beyond-borders' ); ?></li>
            <li><?php esc_html_e( 'Export the list as CSV to import into your email marketing platform (Mailchimp, SendGrid, etc.).', 'beyond-borders' ); ?></li>
            <li><?php esc_html_e( 'A welcome email is automatically sent to new subscribers.', 'beyond-borders' ); ?></li>
        </ul>
    </div>
</div>
