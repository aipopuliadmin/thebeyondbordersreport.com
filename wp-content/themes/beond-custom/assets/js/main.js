/**
 * Main JavaScript
 *
 * @package Beond_Custom
 */

(function($) {
    'use strict';

    // Note: Theme toggle is now in assets/js/header/header-modern.js

    // Mobile menu toggle
    $('.menu-toggle').on('click', function() {
        $(this).toggleClass('active');
        $('.main-navigation').toggleClass('active');
        $('body').toggleClass('menu-open');
    });

    // Search modal (handled by header JS, but keeping modal close functionality)
    $('.search-close, .search-modal').on('click', function(e) {
        if (e.target === this) {
            $('#search-modal').removeClass('active');
        }
    });

    // Back to top button - Floating with smooth animation
    const backToTop = $('#back-to-top');
    
    backToTop.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 800, 'swing');
    });

    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 300) {
            backToTop.addClass('show');
        } else {
            backToTop.removeClass('show');
        }
    });

    // Fallback: Vanilla JavaScript for back-to-top (in case jQuery fails)
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopBtn = document.getElementById('back-to-top');
        
        if (backToTopBtn) {
            // Click handler
            backToTopBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // Scroll handler
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    if (window.pageYOffset > 300) {
                        backToTopBtn.classList.add('show');
                    } else {
                        backToTopBtn.classList.remove('show');
                    }
                }, 10);
            });
        }
    });

    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 600);
        }
    });

    // Related Posts Slider
    document.addEventListener('DOMContentLoaded', function() {
        let currentSlide = 0;
        const slider = document.getElementById('related-posts-slider');
        
        if (slider) {
            const prevBtn = document.querySelector('.prev-arrow[data-slider="related-posts"]');
            const nextBtn = document.querySelector('.next-arrow[data-slider="related-posts"]');
            
            function getCardsPerView() {
                if (window.innerWidth > 1024) return 4;
                if (window.innerWidth > 640) return 2;
                return 1;
            }
            
            function updateSlider() {
                const cards = slider.querySelectorAll('.related-post-card');
                const totalCards = cards.length;
                const cardsPerView = getCardsPerView();
                const maxSlide = Math.max(0, totalCards - cardsPerView);
                
                if (cards.length === 0) return;
                
                const cardWidth = cards[0].offsetWidth;
                const gap = 24; // CSS gap value
                const offset = currentSlide * (cardWidth + gap);
                
                slider.style.transform = `translateX(-${offset}px)`;
                
                // Update button states
                if (prevBtn) {
                    prevBtn.disabled = currentSlide === 0;
                }
                if (nextBtn) {
                    nextBtn.disabled = currentSlide >= maxSlide;
                }
                
                // Clamp current slide to valid range
                if (currentSlide > maxSlide) {
                    currentSlide = maxSlide;
                }
            }
            
            // Previous button
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    if (currentSlide > 0) {
                        currentSlide--;
                        updateSlider();
                    }
                });
            }
            
            // Next button
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    const cards = slider.querySelectorAll('.related-post-card');
                    const cardsPerView = getCardsPerView();
                    const maxSlide = Math.max(0, cards.length - cardsPerView);
                    
                    if (currentSlide < maxSlide) {
                        currentSlide++;
                        updateSlider();
                    }
                });
            }
            
            // Initial state
            updateSlider();
            
            // Update on window resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    currentSlide = 0;
                    updateSlider();
                }, 250);
            });
        }
    });


})(jQuery);
