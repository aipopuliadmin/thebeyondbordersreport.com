/**
 * Modern Header JavaScript
 * Theme toggle and header interactions
 * 
 * @package Beond_Custom
 */

(function() {
    'use strict';

    // ============================================
    // THEME TOGGLE FUNCTIONALITY
    // ============================================
    
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;
    
    // Logo switching elements
    const lightLogos = document.querySelectorAll('.logo-light');
    const darkLogos = document.querySelectorAll('.logo-dark');
    
    // Function to update logo visibility based on theme
    function updateLogoVisibility(theme) {
        if (lightLogos.length && darkLogos.length) {
            if (theme === 'dark') {
                lightLogos.forEach(logo => logo.style.display = 'none');
                darkLogos.forEach(logo => logo.style.display = 'block');
            } else {
                lightLogos.forEach(logo => logo.style.display = 'block');
                darkLogos.forEach(logo => logo.style.display = 'none');
            }
        }
    }
    
    if (themeToggle) {
        // Get default theme from WordPress (passed via wp_localize_script if needed)
        // Or fallback to 'light'
        const defaultTheme = window.beondHeaderSettings?.defaultTheme || 'light';
        
        // Check for saved theme preference or use default
        const currentTheme = localStorage.getItem('theme') || defaultTheme;
        htmlElement.setAttribute('data-theme', currentTheme);
        updateLogoVisibility(currentTheme);
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateLogoVisibility(newTheme);
            
            // Add rotation animation
            themeToggle.style.transform = 'rotate(360deg)';
            setTimeout(function() {
                themeToggle.style.transform = '';
            }, 300);
        });
        
        // Optional: Detect system preference on first visit
        if (!localStorage.getItem('theme')) {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark && defaultTheme === 'light') {
                // Only auto-switch if user prefers dark and default is light
                htmlElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                updateLogoVisibility('dark');
            }
        }
    }

    // ============================================
    // SEARCH TOGGLE FUNCTIONALITY
    // ============================================
    
    const searchToggle = document.querySelector('.search-toggle');
    const searchModal = document.getElementById('search-modal');
    const searchClose = document.querySelector('.search-close');
    
    if (searchToggle && searchModal) {
        // Open search modal
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            searchModal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
            
            // Focus search field after animation
            setTimeout(function() {
                const searchField = searchModal.querySelector('.search-field');
                if (searchField) {
                    searchField.focus();
                }
            }, 100);
        });
        
        // Close search modal
        if (searchClose) {
            searchClose.addEventListener('click', function(e) {
                e.preventDefault();
                searchModal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
        
        // Close on background click
        searchModal.addEventListener('click', function(e) {
            if (e.target === searchModal) {
                searchModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchModal.classList.contains('active')) {
                searchModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // ============================================
    // STICKY HEADER ON SCROLL
    // ============================================
    
    const modernHeader = document.querySelector('.modern-header');
    
    if (modernHeader) {
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100) {
                modernHeader.classList.add('is-scrolled');
            } else {
                modernHeader.classList.remove('is-scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
    }

    // ============================================
    // ACTIVE MENU ITEM HIGHLIGHTING
    // ============================================
    
    const headerNav = document.querySelector('.header-nav');
    
    if (headerNav) {
        const currentUrl = window.location.href;
        const menuLinks = headerNav.querySelectorAll('a');
        
        menuLinks.forEach(function(link) {
            if (link.href === currentUrl) {
                link.classList.add('active');
            }
        });
    }

    // ============================================
    // MOBILE MENU TOGGLE
    // ============================================
    
    // Mobile Menu Overlay Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const mobileMenuClose = mobileMenuOverlay ? mobileMenuOverlay.querySelector('.mobile-menu-close') : null;
    
    if (mobileMenuToggle && mobileMenuOverlay) {
        // Open mobile menu
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mobileMenuOverlay.classList.add('active');
            document.body.classList.add('mobile-menu-open');
        });
        
        // Close mobile menu
        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function(e) {
                e.preventDefault();
                mobileMenuOverlay.classList.remove('active');
                document.body.classList.remove('mobile-menu-open');
                
                // Close all open submenus
                const openSubmenus = mobileMenuOverlay.querySelectorAll('.mobile-menu-item.submenu-active');
                openSubmenus.forEach(function(item) {
                    item.classList.remove('submenu-active');
                });
            });
        }
        
        // Handle accordion submenu toggles with event delegation
        mobileMenuOverlay.addEventListener('click', function(e) {
            const toggle = e.target.closest('.mobile-submenu-toggle');
            
            if (toggle) {
                e.preventDefault();
                e.stopPropagation();
                
                // Get the menu item that owns this toggle button
                const menuItem = toggle.closest('.mobile-menu-item');
                const isCurrentlyOpen = menuItem.classList.contains('submenu-active');
                
                // Get the direct parent container (either .mobile-menu-list or .mobile-submenu)
                const parentContainer = menuItem.parentElement;
                
                // Get direct sibling menu items (children of the same parent container)
                const siblings = Array.from(parentContainer.children).filter(function(child) {
                    return child.classList.contains('mobile-menu-item') && child !== menuItem;
                });
                
                // Close all sibling items and their children
                siblings.forEach(function(sibling) {
                    sibling.classList.remove('submenu-active');
                    // Close all descendants
                    const descendants = sibling.querySelectorAll('.mobile-menu-item.submenu-active');
                    descendants.forEach(function(desc) {
                        desc.classList.remove('submenu-active');
                    });
                });
                
                // Toggle current item
                if (isCurrentlyOpen) {
                    // Close this item
                    menuItem.classList.remove('submenu-active');
                    // Close all descendants
                    const descendants = menuItem.querySelectorAll('.mobile-menu-item.submenu-active');
                    descendants.forEach(function(desc) {
                        desc.classList.remove('submenu-active');
                    });
                } else {
                    // Open this item
                    menuItem.classList.add('submenu-active');
                }
            }
        });
    }
    
    // ============================================
    // DESKTOP MENU DROPDOWN (3-Level Support)
    // ============================================
    
    const desktopMenuNav = document.querySelector('.header-nav');
    
    if (desktopMenuNav) {
        // Add column classes based on item count
        const allSubmenus = desktopMenuNav.querySelectorAll('.sub-menu');
        allSubmenus.forEach(submenu => {
            const itemCount = submenu.querySelectorAll(':scope > li').length;
            if (itemCount >= 13) {
                submenu.classList.add('has-many-items'); // 3 columns
            } else if (itemCount >= 6) {
                submenu.classList.add('has-medium-items'); // 2 columns
            } else {
                submenu.classList.add('has-few-items'); // 1 column
            }
        });
        
        // Handle all menu items with children (level 1, 2, 3)
        desktopMenuNav.addEventListener('click', function(e) {
            // Only handle on desktop (> 992px)
            if (window.innerWidth <= 992) return;
            
            const clickedLink = e.target.closest('a');
            if (!clickedLink) return;
            
            const menuItem = clickedLink.closest('.menu-item-has-children');
            
            // If clicked link is NOT part of a menu item with children, allow navigation
            if (!menuItem) return;
            
            // Check if the clicked link is the direct child of menu-item-has-children
            // If it is, prevent default and toggle submenu
            const directLink = menuItem.querySelector(':scope > a');
            if (clickedLink === directLink) {
                e.preventDefault();
                e.stopPropagation();
                
                const isCurrentlyOpen = menuItem.classList.contains('submenu-open');
                
                // Close ALL sibling menus at the same level
                const parentUl = menuItem.parentElement;
                const siblings = Array.from(parentUl.children).filter(child => 
                    child.classList.contains('menu-item-has-children') && child !== menuItem
                );
                
                siblings.forEach(sibling => {
                    sibling.classList.remove('submenu-open');
                    // Also close all descendant submenus
                    const descendants = sibling.querySelectorAll('.submenu-open');
                    descendants.forEach(desc => desc.classList.remove('submenu-open'));
                });
                
                // Toggle current menu
                if (isCurrentlyOpen) {
                    menuItem.classList.remove('submenu-open');
                    // Close all descendant submenus
                    const descendants = menuItem.querySelectorAll('.submenu-open');
                    descendants.forEach(desc => desc.classList.remove('submenu-open'));
                } else {
                    menuItem.classList.add('submenu-open');
                }
            }
            // If clicking a nested link (not the direct parent), let it navigate
        });
        
        // Close all dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header-nav') && window.innerWidth > 992) {
                const allOpenMenus = desktopMenuNav.querySelectorAll('.submenu-open');
                allOpenMenus.forEach(item => item.classList.remove('submenu-open'));
            }
        });
        
        // Close all on window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 992) {
                    const allOpenMenus = desktopMenuNav.querySelectorAll('.submenu-open');
                    allOpenMenus.forEach(item => item.classList.remove('submenu-open'));
                }
            }, 250);
        });
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && window.innerWidth > 992) {
                const allOpenMenus = desktopMenuNav.querySelectorAll('.submenu-open');
                allOpenMenus.forEach(item => item.classList.remove('submenu-open'));
            }
        });
    }
    
    function updateHamburgerIcon(isOpen) {
        const toggle = document.querySelector('.mobile-menu-toggle');
        if (!toggle) return;
        
        const svg = toggle.querySelector('svg');
        if (!svg) return;
        
        if (isOpen) {
            // Change to X icon
            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        } else {
            // Change to hamburger icon
            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
        }
    }

})();

