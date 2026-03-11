<?php
/**
 * Template Name: About Us Page
 * 
 * @package Beond_Custom
 */

get_header();
?>

<main id="primary" class="site-main about-page">

    <?php
    // Get dynamic hero content
    $hero_label = get_post_meta( get_the_ID(), '_about_hero_label', true );
    $hero_title = get_post_meta( get_the_ID(), '_about_hero_title', true );
    $hero_subtitle = get_post_meta( get_the_ID(), '_about_hero_subtitle', true );
    
    // Get statistics
    $stat1_number = get_post_meta( get_the_ID(), '_about_stat1_number', true );
    $stat1_label = get_post_meta( get_the_ID(), '_about_stat1_label', true );
    $stat2_number = get_post_meta( get_the_ID(), '_about_stat2_number', true );
    $stat2_label = get_post_meta( get_the_ID(), '_about_stat2_label', true );
    $stat3_number = get_post_meta( get_the_ID(), '_about_stat3_number', true );
    $stat3_label = get_post_meta( get_the_ID(), '_about_stat3_label', true );
    $stat4_number = get_post_meta( get_the_ID(), '_about_stat4_number', true );
    $stat4_label = get_post_meta( get_the_ID(), '_about_stat4_label', true );
    
    // Set defaults if empty
    $hero_label = $hero_label ?: 'About Us';
    $hero_title = $hero_title ?: 'Curated Insights for a Borderless World';
    $hero_subtitle = $hero_subtitle ?: "Beyond Borders Report is your premier source for in-depth analysis and expert perspectives on global business, leadership, and innovation. We connect industry leaders with the insights that matter most.";
    $stat1_number = $stat1_number ?: '200K+';
    $stat1_label = $stat1_label ?: 'Global Readers';
    $stat2_number = $stat2_number ?: '500+';
    $stat2_label = $stat2_label ?: 'Expert Contributors';
    $stat3_number = $stat3_number ?: '50+';
    $stat3_label = $stat3_label ?: 'Industries Covered';
    $stat4_number = $stat4_number ?: '10+';
    $stat4_label = $stat4_label ?: 'Years of Excellence';
    ?>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <span class="hero-label"><?php echo esc_html( $hero_label ); ?></span>
            <h1 class="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
            <p class="hero-subtitle">
                <?php echo esc_html( $hero_subtitle ); ?>
            </p>
            
            <div class="hero-stats" style="display:none">
                <div class="stat-item">
                    <div class="stat-number"><?php echo esc_html( $stat1_number ); ?></div>
                    <div class="stat-label"><?php echo esc_html( $stat1_label ); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo esc_html( $stat2_number ); ?></div>
                    <div class="stat-label"><?php echo esc_html( $stat2_label ); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo esc_html( $stat3_number ); ?></div>
                    <div class="stat-label"><?php echo esc_html( $stat3_label ); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo esc_html( $stat4_number ); ?></div>
                    <div class="stat-label"><?php echo esc_html( $stat4_label ); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Mission</span>
                <h2 class="section-title">Empowering Leaders Through Knowledge</h2>
                <p class="section-description">
                    We believe in the power of informed decision-making. Our mission is to deliver 
                    premium, actionable insights that help business leaders navigate an increasingly 
                    complex global landscape.
                </p>
            </div>

            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon">🎯</div>
                    <h3>Editorial Excellence</h3>
                    <p>
                        Every piece of content is meticulously researched, fact-checked, and crafted 
                        to provide genuine value. We maintain the highest standards of journalistic 
                        integrity and editorial quality.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">🌍</div>
                    <h3>Global Perspective</h3>
                    <p>
                        With contributors across six continents, we bring diverse viewpoints and 
                        cross-cultural insights that help you see the bigger picture in international 
                        business and leadership.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">💡</div>
                    <h3>Innovation Focus</h3>
                    <p>
                        We stay ahead of trends, tracking emerging technologies, business models, 
                        and leadership paradigms that are shaping the future of work and commerce.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="story-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Story</span>
                <h2 class="section-title">A Decade of Impact</h2>
            </div>

            <div class="story-content">
                <div class="story-image">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/about-office.jpg' ); ?>" 
                         alt="Modern Office" 
                         onerror="this.src='https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop'">
                </div>
                <div class="story-text">
                    <h3>From Vision to Leading Voice</h3>
                    <p>
                        Founded in 2016 by a team of seasoned journalists and business strategists, 
                        Beyond Borders Report emerged from a simple observation: <span class="highlight">the world 
                        needed a platform that transcended geographical and ideological boundaries</span> to 
                        deliver truly global business intelligence.
                    </p>
                    <p>
                        What started as a modest newsletter for 500 subscribers has grown into a 
                        multimedia platform reaching over 200,000 industry leaders monthly. Our content 
                        has been cited by Fortune 500 companies, featured in major media outlets, and 
                        trusted by decision-makers worldwide.
                    </p>
                    <p>
                        Today, we're proud to be recognized as one of the <span class="highlight">most trusted 
                        sources for global business insights</span>, combining rigorous journalism with 
                        expert analysis to help leaders make informed decisions in an uncertain world.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Values</span>
                <h2 class="section-title">What We Stand For</h2>
                <p class="section-description">
                    Our core values guide everything we do, from content creation to community engagement.
                </p>
            </div>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-number">01</div>
                    <h3>Integrity First</h3>
                    <p>
                        We never compromise on accuracy, transparency, or ethical journalism. 
                        Our readers trust us because we've earned it.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-number">02</div>
                    <h3>Diversity of Thought</h3>
                    <p>
                        We actively seek out diverse perspectives and challenge conventional 
                        wisdom to provide balanced, nuanced analysis.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-number">03</div>
                    <h3>Reader-Centric</h3>
                    <p>
                        Your time is valuable. We deliver concise, actionable insights without 
                        fluff or unnecessary jargon.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-number">04</div>
                    <h3>Continuous Learning</h3>
                    <p>
                        The world evolves, and so do we. We're committed to staying curious 
                        and adapting to serve our readers better.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Journey</span>
                <h2 class="section-title">Key Milestones</h2>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2025</div>
                        <h4>Idea generation</h4>
                        <p>
                            Beyond Borders Report launched with a weekly newsletter reaching 
                            500 business leaders across North America and Europe.
                        </p>
                    </div>
                    <div class="timeline-dot"></div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2026</div>
                        <h4>Launch year</h4>
                        <p>
                            Expanded coverage to Asia-Pacific and Middle East markets, establishing 
                            regional editorial teams and reaching 50,000 subscribers.
                        </p>
                    </div>
                    <div class="timeline-dot"></div>
                </div>

                <!-- <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2020</div>
                        <h4>Digital Transformation</h4>
                        <p>
                            Launched multimedia platform with podcasts, video interviews, and 
                            interactive data visualizations. Reader base grew to 150,000.
                        </p>
                    </div>
                    <div class="timeline-dot"></div>
                </div> -->

                <!-- <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2023</div>
                        <h4>Industry Recognition</h4>
                        <p>
                            Received "Best Business Publication" award and established exclusive 
                            partnerships with leading business schools and think tanks.
                        </p>
                    </div>
                    <div class="timeline-dot"></div>
                </div> -->

                <!-- <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2026</div>
                        <h4>AI-Enhanced Insights</h4>
                        <p>
                            Integrated advanced AI tools for personalized content recommendations 
                            while maintaining human editorial oversight and reaching 200,000+ readers.
                        </p>
                    </div>
                    <div class="timeline-dot"></div>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Team</span>
                <h2 class="section-title">Meet the Leaders</h2>
                <p class="section-description">
                    Our editorial team brings together decades of experience in journalism, 
                    business strategy, and global affairs.
                </p>
            </div>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/sharathkanth.png' ); ?>" alt="Editor-in-Chief">
                    </div>
                    <div class="team-info">
                        <h3 class="team-name">Sharath Kanth</h3>
                        <div class="team-role">Editor-in-Chief</div>
                        <p class="team-bio">
                            Travel Retail Specialist
                            With over 15+ years of experience in Fashion Retail, Travel Retail, and Airport Commercial Management, I currently serve as the country commercial director for Dufry India (Avolta Group), driving brand strategy & innovation
                        </p>
                        <div class="team-social">
                            <a href="http://www.linkedin.com/in/sharathkanth" aria-label="LinkedIn" target="_blank">in</a>
                            <a href="https://www.youtube.com/@sharathkanth9757" aria-label="Youtube" target="_blank">▶</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- #primary -->

<?php
get_footer();
