<?php
/**
 * Template Name: Contact Us
 * Description: Contact page template with form and information
 *
 * @package Beond_Custom
 */

get_header(); ?>

<div class="contact-page">
    <?php
    // Get dynamic hero content
    $hero_label = get_post_meta( get_the_ID(), '_contact_hero_label', true );
    $hero_title = get_post_meta( get_the_ID(), '_contact_hero_title', true );
    $hero_subtitle = get_post_meta( get_the_ID(), '_contact_hero_subtitle', true );
    
    // Get dynamic contact information
    $email = get_post_meta( get_the_ID(), '_contact_email', true );
    $press_email = get_post_meta( get_the_ID(), '_contact_press_email', true );
    $phone = get_post_meta( get_the_ID(), '_contact_phone', true );
    $phone_hours = get_post_meta( get_the_ID(), '_contact_phone_hours', true );
    $phone2 = get_post_meta( get_the_ID(), '_contact_phone2', true );
    $phone2_hours = get_post_meta( get_the_ID(), '_contact_phone2_hours', true );
    $address_line1 = get_post_meta( get_the_ID(), '_contact_address_line1', true );
    $address_line2 = get_post_meta( get_the_ID(), '_contact_address_line2', true );
    $address_note = get_post_meta( get_the_ID(), '_contact_address_note', true );
    
    // Get social media links
    $social_links = array(
        'twitter' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_twitter', true ),
            'label' => 'Twitter'
        ),
        'facebook' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_facebook', true ),
            'label' => 'Facebook'
        ),
        'instagram' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_instagram', true ),
            'label' => 'Instagram'
        ),
        'linkedin' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_linkedin', true ),
            'label' => 'LinkedIn'
        ),
        'youtube' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_youtube', true ),
            'label' => 'YouTube'
        ),
        'tiktok' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_tiktok', true ),
            'label' => 'TikTok'
        ),
        'pinterest' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_pinterest', true ),
            'label' => 'Pinterest'
        ),
        'whatsapp' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_whatsapp', true ),
            'label' => 'WhatsApp'
        ),
        'telegram' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_telegram', true ),
            'label' => 'Telegram'
        ),
        'snapchat' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_snapchat', true ),
            'label' => 'Snapchat'
        ),
        'reddit' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_reddit', true ),
            'label' => 'Reddit'
        ),
        'github' => array(
            'url' => get_post_meta( get_the_ID(), '_contact_github', true ),
            'label' => 'GitHub'
        )
    );
    
    // Filter out empty social links
    $social_links = array_filter( $social_links, function( $link ) {
        return ! empty( $link['url'] );
    });
    
    // Set defaults if empty
    $hero_label = $hero_label ?: 'GET IN TOUCH';
    $hero_title = $hero_title ?: 'Contact Beyond Borders';
    $hero_subtitle = $hero_subtitle ?: "Have a question, story idea, or just want to say hello? We'd love to hear from you. Our team is here to help and typically responds within 24 hours.";
    $email = $email ?: 'info@beyondborders.com';
    $press_email = $press_email ?: 'press@beyondborders.com';
    $phone = $phone ?: '+1 (555) 123-4567';
    $phone_hours = $phone_hours ?: 'Mon-Fri, 9am-6pm EST';
    $address_line1 = $address_line1 ?: '123 Global News Avenue';
    $address_line2 = $address_line2 ?: 'New York, NY 10001';
    $address_note = $address_note ?: 'By appointment only';
    
    // Get FAQ data
    $faq_label = get_post_meta( get_the_ID(), '_contact_faq_label', true );
    $faq_title = get_post_meta( get_the_ID(), '_contact_faq_title', true );
    $faqs = get_post_meta( get_the_ID(), '_contact_faqs', true );
    
    // Set FAQ defaults if empty
    $faq_label = $faq_label ?: 'FAQ';
    $faq_title = $faq_title ?: 'Frequently Asked Questions';
    
    if ( ! is_array( $faqs ) || empty( $faqs ) ) {
        $faqs = array(
            array(
                'question' => 'How quickly will I receive a response?',
                'answer' => 'We typically respond to all inquiries within 24 hours during business days. For urgent press matters, please call our media hotline directly.'
            ),
            array(
                'question' => 'Can I submit a story idea?',
                'answer' => 'Absolutely! We welcome story submissions from our readers. Please use the "Story Submission" subject when contacting us and provide as much detail as possible.'
            ),
            array(
                'question' => 'Do you accept guest contributions?',
                'answer' => 'Yes, we occasionally publish guest articles from subject matter experts. Please select "Partnership Opportunity" and include writing samples with your inquiry.'
            ),
            array(
                'question' => 'How can I report an error in an article?',
                'answer' => 'We take accuracy seriously. Please email us with the article URL and details of the error. We review all corrections promptly and update articles as needed.'
            )
        );
    }
    ?>
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <span class="hero-label"><?php echo esc_html( $hero_label ); ?></span>
            <h1 class="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
            <p class="hero-subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
        </div>
    </section>

    <!-- Contact Info & Form Section -->
    <section class="contact-main">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h2 class="section-title">Let's Connect</h2>
                    <p class="section-subtitle">Choose the best way to reach us.</p>

                    <div class="info-cards">
                        <div class="info-card">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 8L10.89 13.26C11.2187 13.4793 11.6049 13.5963 12 13.5963C12.3951 13.5963 12.7813 13.4793 13.11 13.26L21 8M5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7C21 6.46957 20.7893 5.96086 20.4142 5.58579C20.0391 5.21071 19.5304 5 19 5H5C4.46957 5 3.96086 5.21071 3.58579 5.58579C3.21071 5.96086 3 6.46957 3 7V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="info-content">
                                <h3>Email Us</h3>
                                <p><?php echo esc_html( $email ); ?></p>
                                <p class="info-note">For press inquiries: <?php echo esc_html( $press_email ); ?></p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 5C3 3.89543 3.89543 3 5 3H8.27924C8.70967 3 9.09181 3.27543 9.22792 3.68377L10.7257 8.17721C10.8831 8.64932 10.6694 9.16531 10.2243 9.38787L7.96701 10.5165C9.06925 12.9612 11.0388 14.9308 13.4835 16.033L14.6121 13.7757C14.8347 13.3306 15.3507 13.1169 15.8228 13.2743L20.3162 14.7721C20.7246 14.9082 21 15.2903 21 15.7208V19C21 20.1046 20.1046 21 19 21H18C9.71573 21 3 14.2843 3 6V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="info-content">
                                <h3>Call Us</h3>
                                <p><?php echo esc_html( $phone ); ?></p>
                                <p class="info-note"><?php echo esc_html( $phone_hours ); ?></p>
                                <?php if ( ! empty( $phone2 ) ) : ?>
                                    <p style="margin-top: 12px;"><?php echo esc_html( $phone2 ); ?></p>
                                    <?php if ( ! empty( $phone2_hours ) ) : ?>
                                        <p class="info-note"><?php echo esc_html( $phone2_hours ); ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.657 16.657L13.414 20.9C13.039 21.275 12.5306 21.4854 12 21.4854C11.4694 21.4854 10.961 21.275 10.586 20.9L6.343 16.657C5.22422 15.5381 4.46234 14.1127 4.15369 12.5608C3.84504 11.009 4.00349 9.40047 4.60901 7.93868C5.21452 6.4769 6.2399 5.22749 7.55548 4.34846C8.87107 3.46943 10.4178 3.00024 12 3.00024C13.5822 3.00024 15.1289 3.46943 16.4445 4.34846C17.7601 5.22749 18.7855 6.4769 19.391 7.93868C19.9965 9.40047 20.155 11.009 19.8463 12.5608C19.5377 14.1127 18.7758 15.5381 17.657 16.657Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="info-content">
                                <h3>Visit Us</h3>
                                <p><?php echo esc_html( $address_line1 ); ?></p>
                                <p><?php echo esc_html( $address_line2 ); ?></p>
                                <p class="info-note"><?php echo esc_html( $address_note ); ?></p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 12H8.01M12 12H12.01M16 12H16.01M21 12C21 16.4183 16.9706 20 12 20C10.4607 20 9.01172 19.6565 7.74467 19.0511L3 20L4.39499 16.28C3.51156 15.0423 3 13.5743 3 12C3 7.58172 7.02944 4 12 4C16.9706 4 21 7.58172 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="info-content">
                                <h3>Social Media</h3>
                                <?php if ( ! empty( $social_links ) ) : ?>
                                    <div class="social-links">
                                        <?php foreach ( $social_links as $platform => $link ) : ?>
                                            <a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $link['label'] ); ?>">
                                                <?php echo esc_html( $link['label'] ); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <p class="info-note">Follow us on social media</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <div class="form-header">
                        <h2>Send Us a Message</h2>
                        <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                    </div>

                    <form class="contact-form" id="contactForm">
                        <?php wp_nonce_field( 'contact_form_nonce', 'contact_nonce' ); ?>
                        
                        <!-- Honeypot field - hidden from users -->
                        <input type="text" name="website" id="website" style="display: none !important;" tabindex="-1" autocomplete="off">
                        
                        <!-- Timestamp for time-based validation -->
                        <input type="hidden" name="form_timestamp" id="form_timestamp" value="">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">First Name <span class="required">*</span></label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name <span class="required">*</span></label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span class="required">*</span></label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="press">Press/Media Request</option>
                                <option value="story">Story Submission</option>
                                <option value="partnership">Partnership Opportunity</option>
                                <option value="technical">Technical Support</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="newsletter" id="newsletter">
                                <span>Subscribe to our newsletter for weekly updates</span>
                            </label>
                        </div>

                        <button type="submit" class="submit-btn">
                            <span class="btn-text">Send Message</span>
                            <svg class="btn-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div class="form-message" id="formMessage"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="contact-faq">
        <div class="container">
            <div class="section-header">
                <span class="section-label"><?php echo esc_html( $faq_label ); ?></span>
                <h2 class="section-title"><?php echo esc_html( $faq_title ); ?></h2>
            </div>

            <div class="faq-grid">
                <?php foreach ( $faqs as $faq ) : ?>
                    <div class="faq-item">
                        <h3><?php echo esc_html( $faq['question'] ); ?></h3>
                        <p><?php echo esc_html( $faq['answer'] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Set form timestamp when page loads
        $('#form_timestamp').val(Date.now());
        
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $form.find('.submit-btn');
            var $message = $('#formMessage');
            var originalBtnText = $submitBtn.find('.btn-text').text();
            
            // Disable submit button
            $submitBtn.prop('disabled', true);
            $submitBtn.find('.btn-text').text('Sending...');
            $message.hide().removeClass('success error');
            
            // Prepare form data
            var formData = {
                action: 'submit_contact_form',
                nonce: $('#contact_nonce').val(),
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                subject: $('#subject').val(),
                message: $('#message').val(),
                newsletter: $('#newsletter').is(':checked') ? 1 : 0,
                website: $('#website').val(), // Honeypot
                form_timestamp: $('#form_timestamp').val() // Timestamp
            };
            
            // Submit via AJAX
            $.ajax({
                url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                type: 'POST',
                data: formData,
                timeout: 30000,
                success: function(response, textStatus, jqXHR) {
                    console.log('Raw response:', response);
                    console.log('Response type:', typeof response);
                    
                    // Handle string responses (shouldn't happen with dataType: 'json', but just in case)
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch(e) {
                            console.error('Failed to parse response:', e);
                            $message.addClass('error').text('Invalid server response. Please contact support.').fadeIn();
                            return;
                        }
                    }
                    
                    if (response && response.success) {
                        $message.addClass('success').text(response.data.message).fadeIn();
                        $form[0].reset();
                    } else if (response && response.data && response.data.message) {
                        $message.addClass('error').text(response.data.message).fadeIn();
                    } else {
                        $message.addClass('error').text('An error occurred. Please try again.').fadeIn();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', textStatus);
                    console.error('Error:', errorThrown);
                    console.error('HTTP Status:', jqXHR.status);
                    console.error('Response Text:', jqXHR.responseText);
                    console.error('Response Headers:', jqXHR.getAllResponseHeaders());
                    
                    // Try to parse response - server might have sent JSON with error status
                    if (jqXHR.responseText) {
                        try {
                            var response = JSON.parse(jqXHR.responseText);
                            console.log('Parsed error response:', response);
                            
                            if (response && response.success) {
                                $message.addClass('success').text(response.data.message).fadeIn();
                                $form[0].reset();
                                return;
                            } else if (response && response.data && response.data.message) {
                                $message.addClass('error').text(response.data.message).fadeIn();
                                return;
                            }
                        } catch(e) {
                            console.error('JSON parse failed:', e);
                        }
                    }
                    
                    // Specific error messages
                    var errorMessage = 'An error occurred. Please try again.';
                    
                    if (textStatus === 'timeout') {
                        errorMessage = 'Request timed out. Please try again.';
                    } else if (textStatus === 'error') {
                        if (jqXHR.status === 0) {
                            errorMessage = 'Network error. Please check your internet connection.';
                        } else if (jqXHR.status === 404) {
                            errorMessage = 'Server endpoint not found. Please contact support.';
                        } else if (jqXHR.status === 500) {
                            errorMessage = 'Server error. Please try again later or contact support.';
                        } else if (jqXHR.status === 403) {
                            errorMessage = 'Access denied. Please refresh the page and try again.';
                        } else {
                            errorMessage = 'Server error (' + jqXHR.status + '). Please try again.';
                        }
                    } else if (textStatus === 'parsererror') {
                        errorMessage = 'Invalid server response. Please contact support.';
                    }
                    
                    $message.addClass('error').text(errorMessage).fadeIn();
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                    $submitBtn.find('.btn-text').text(originalBtnText);
                }
            });
        });
    });
})(jQuery);
</script>

<?php get_footer(); ?>
