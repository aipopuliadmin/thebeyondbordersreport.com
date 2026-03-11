<?php
/**
 * Login Page Template
 */

// Start session if not already started
if ( ! session_id() ) {
    session_start();
}

get_header();
?>

<div class="bb-auth-page bb-login-page">
    <div class="bb-auth-container">
        <div class="auth-header">
            <h1>Login to Beyond Borders</h1>
            <p>Access your account</p>
        </div>

        <?php if ( isset( $_SESSION['bb_login_error'] ) ) : ?>
            <div class="auth-error">
                <?php echo esc_html( $_SESSION['bb_login_error'] ); ?>
                <?php unset( $_SESSION['bb_login_error'] ); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bb-login-form">
            <?php wp_nonce_field( 'bb_login_action', 'bb_login_nonce' ); ?>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn-primary btn-submit">Login</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="<?php echo esc_url( home_url( '/user/register' ) ); ?>">Register here</a></p>
        </div>
    </div>
</div>

<?php get_footer();
