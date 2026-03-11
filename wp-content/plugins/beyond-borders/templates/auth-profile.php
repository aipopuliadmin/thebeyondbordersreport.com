<?php
/**
 * User Profile Page Template
 */

// Start session if not already started
if ( ! session_id() ) {
    session_start();
}

if ( ! is_user_logged_in() ) {
    wp_safe_remote_redirect( home_url( '/user/login' ) );
    exit;
}

$user_id = get_current_user_id();
$user = get_userdata( $user_id );
$job_title = get_user_meta( $user_id, 'job_title_bb', true );
$company = get_user_meta( $user_id, 'company_bb', true );
$bio = get_user_meta( $user_id, 'bio_bb', true );
$profile_image_url = Beyond_Borders_Auth::get_user_profile_image( $user_id, 'medium' );

get_header();
?>

<div class="bb-profile-page">
    <div class="bb-profile-container">
        <div class="profile-header">
            <div class="profile-header-content">
                <h1>Welcome, <?php echo esc_html( $user->first_name ); ?> 👋</h1>
                <p>Manage your Beyond Borders account</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/user/logout' ) ); ?>" class="btn-logout">Logout</a>
        </div>

        <?php if ( isset( $_SESSION['bb_profile_success'] ) ) : ?>
            <div class="auth-success">
                <?php echo esc_html( $_SESSION['bb_profile_success'] ); ?>
                <?php unset( $_SESSION['bb_profile_success'] ); ?>
            </div>
        <?php endif; ?>

        <div class="profile-content">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-image-section">
                    <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="profile-image">
                </div>

                <div class="profile-info-summary">
                    <h2><?php echo esc_html( $user->first_name . ' ' . $user->last_name ); ?></h2>
                    <?php if ( $job_title ) : ?>
                        <p class="job-title"><?php echo esc_html( $job_title ); ?></p>
                    <?php endif; ?>
                    <?php if ( $company ) : ?>
                        <p class="company"><?php echo esc_html( $company ); ?></p>
                    <?php endif; ?>
                    <p class="email"><?php echo esc_html( $user->user_email ); ?></p>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="edit-profile-section">
                <h2>Edit Profile</h2>

                <form method="POST" enctype="multipart/form-data" class="bb-profile-form">
                    <?php wp_nonce_field( 'bb_profile_update', 'bb_profile_nonce' ); ?>

                    <!-- Basic Information -->
                    <fieldset class="form-fieldset">
                        <legend>Basic Information</legend>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr( $user->first_name ); ?>">
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr( $user->last_name ); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email Address (cannot be changed here)</label>
                            <input type="email" value="<?php echo esc_attr( $user->user_email ); ?>" disabled>
                            <small>Contact support to change your email</small>
                        </div>
                    </fieldset>

                    <!-- Professional Information -->
                    <fieldset class="form-fieldset">
                        <legend>Professional Information</legend>

                        <div class="form-group">
                            <label for="job_title">Job Title</label>
                            <input type="text" id="job_title" name="job_title" value="<?php echo esc_attr( $job_title ); ?>" placeholder="e.g., Journalist">
                        </div>

                        <div class="form-group">
                            <label for="company">Company</label>
                            <input type="text" id="company" name="company" value="<?php echo esc_attr( $company ); ?>" placeholder="e.g., Your Media Company">
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo esc_textarea( $bio ); ?></textarea>
                        </div>
                    </fieldset>

                    <!-- Profile Image -->
                    <fieldset class="form-fieldset">
                        <legend>Profile Image</legend>

                        <div class="form-group">
                            <label for="profile_image">Upload New Image</label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*">
                            <small>Max file size: 5MB (JPEG, PNG, GIF)</small>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn-primary btn-submit">Update Profile</button>
                </form>
            </div>

            <!-- Account Information -->
            <div class="account-info-section">
                <h2>Account Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Member Since</span>
                        <span class="value"><?php echo esc_html( date_i18n( 'F d, Y', strtotime( $user->user_registered ) ) ); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Username</span>
                        <span class="value"><?php echo esc_html( $user->user_login ); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Posts Published</span>
                        <span class="value"><?php echo count_user_posts( $user_id ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer();
