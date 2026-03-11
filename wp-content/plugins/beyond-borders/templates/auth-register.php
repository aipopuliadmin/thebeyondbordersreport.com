<?php
/**
 * Registration Page Template
 */

// Start session if not already started
if ( ! session_id() ) {
    session_start();
}

get_header();
?>

<div class="bb-auth-page bb-register-page">
    <div class="bb-auth-container">
        <div class="auth-header">
            <h1>Join Beyond Borders</h1>
            <p>Create your account and start contributing</p>
        </div>

        <?php if ( isset( $_SESSION['bb_register_errors'] ) && ! empty( $_SESSION['bb_register_errors'] ) ) : ?>
            <div class="auth-error">
                <ul>
                    <?php foreach ( $_SESSION['bb_register_errors'] as $error ) : ?>
                        <li><?php echo esc_html( $error ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset( $_SESSION['bb_register_errors'] ); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bb-register-form">
            <?php wp_nonce_field( 'bb_register_action', 'bb_register_nonce' ); ?>

            <!-- Basic Information -->
            <fieldset class="form-fieldset">
                <legend>Basic Information</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required placeholder="Enter your first name">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required placeholder="At least 6 characters">
                        <small>Must be at least 6 characters long</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Re-enter your password">
                    </div>
                </div>
            </fieldset>

            <!-- Profile Information -->
            <fieldset class="form-fieldset">
                <legend>Profile Information</legend>

                <div class="form-group">
                    <label for="job_title">Job Title</label>
                    <input type="text" id="job_title" name="job_title" placeholder="e.g., Journalist, Photographer, Editor">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" placeholder="e.g., Your Media Company">
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."></textarea>
                </div>

                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    <small>Max file size: 5MB (JPEG, PNG, GIF)</small>
                </div>
            </fieldset>

            <button type="submit" class="btn-primary btn-submit">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="<?php echo esc_url( home_url( '/user/login' ) ); ?>">Login here</a></p>
        </div>
    </div>
</div>

<?php get_footer();
