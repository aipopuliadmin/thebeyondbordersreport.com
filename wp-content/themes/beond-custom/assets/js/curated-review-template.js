/**
 * Curated Review Content Template
 * 
 * Auto-populates the post editor with Curated Review template
 * when the "Curated Review" category or its children are selected
 */

wp.domReady(() => {
    const { subscribe } = wp.data;
    const { dispatch, select } = wp.data;
    
    // Track if template has been applied for this post
    let templateApplied = false;
    let lastCategories = [];
    
    // Console log to confirm script is running
    console.log('✓ Curated Review Template Script Loaded');
    
    // Curated Review category IDs
    const CURATED_REVIEW_IDS = [24, 25, 26, 27, 28, 29, 30];
    
    // Subscribe to post/category changes
    const unsubscribe = subscribe(() => {
        try {
            // Get current post
            const post = select('core/editor').getCurrentPost();
            
            if (!post) {
                console.log('⚠ No post found');
                return;
            }
            
            // Get post categories - try different methods
            let categories = [];
            
            if (post.categories && Array.isArray(post.categories)) {
                categories = post.categories;
            }
            
            // Also check the raw post meta
            if (categories.length === 0 && post._embedded) {
                // Try alternative detection
                console.log('🔍 Trying alternative category detection...');
            }
            
            // Check if categories changed (debounce unnecessary checks)
            const categoriesChanged = JSON.stringify(categories) !== JSON.stringify(lastCategories);
            
            if (!categoriesChanged) {
                return;
            }
            
            lastCategories = categories;
            
            if (categories.length > 0) {
                console.log('📁 Post categories:', categories);
            }
            
            // Check if post has Curated Review category
            const hasCuratedReviewCategory = categories.some(catId => 
                CURATED_REVIEW_IDS.includes(catId)
            );
            
            console.log('🎯 Has Curated Review category?', hasCuratedReviewCategory);
            
            // If post has Curated Review category and template not yet applied
            if (hasCuratedReviewCategory && !templateApplied) {
                templateApplied = true;
                console.log('✅ Applying Curated Review template...');
                applyTemplate();
            }
            
            // Reset template if category removed
            if (!hasCuratedReviewCategory && templateApplied) {
                templateApplied = false;
                console.log('❌ Template flag reset (category removed)');
            }
        } catch (error) {
            console.error('❌ Error in Curated Review Template:', error);
        }
    });
    
    // Also add a click listener for category checkboxes as fallback
    setTimeout(() => {
        addCategoryCheckboxListeners();
    }, 1000);
    
    /**
     * Apply Curated Review template to the post
     */
    function applyTemplate() {
        try {
            const blocks = wp.blocks;
            const defaultTemplate = getCuratedReviewTemplate();
            
            // Get current blocks
            const currentBlocks = select('core/block-editor').getBlocks();
            console.log('📦 Current blocks:', currentBlocks.length);
            
            // Check if post is mostly empty
            const isEmpty = !currentBlocks || 
                           currentBlocks.length === 0 ||
                           (currentBlocks.length === 1 && 
                            (currentBlocks[0]?.name === 'core/paragraph' || 
                             currentBlocks[0]?.name === 'core/image') &&
                            !currentBlocks[0]?.attributes?.content);
            
            if (isEmpty) {
                console.log('📝 Post is empty, inserting template blocks...');
                
                // Clear existing content
                dispatch('core/block-editor').resetBlocks([]);
                
                // Insert template blocks with small delay
                setTimeout(() => {
                    dispatch('core/block-editor').insertBlocks(defaultTemplate, 0, false);
                    showNotification();
                    console.log('✅ Template applied successfully!');
                }, 100);
            } else {
                console.log('⚠ Post already has content, not overwriting');
                console.log('   (To apply template, create a new post)');
            }
        } catch (error) {
            console.error('❌ Error applying template:', error);
        }
    }
    
    /**
     * Get the Curated Review content template
     * Returns array of Gutenberg blocks matching professional review structure
     */
    function getCuratedReviewTemplate() {
        const blocks = wp.blocks;
        
        return [
            // Header & Product Identity
            blocks.createBlock('core/heading', {
                content: 'Header & Product Identity',
                level: 2
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Product Name:</strong> ________________<br><strong>Brand:</strong> ________________<br><strong>Type:</strong> Category / Type / Variant<br><strong>Price:</strong> USD ___ / Local Currency ___<br><strong>Weight / Pack Size:</strong> _____ g',
            }),
            
            // Visual & Media
            blocks.createBlock('core/heading', {
                content: 'Visual & Media',
                level: 2
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '[Insert product image - packaging, prepared appearance, or serving suggestion]',
            }),
            
            blocks.createBlock('core/image', {
                url: '',
                alt: 'Product image',
                className: 'aligncenter'
            }),
            
            // Quick Facts / Specs - Table
            blocks.createBlock('core/heading', {
                content: 'Quick Facts & Specifications',
                level: 2
            }),
            
            blocks.createBlock('core/table', {
                hasFixedLayout: false,
                body: [
                    {
                        cells: [
                            { content: 'Attribute', tag: 'th' },
                            { content: 'Detail', tag: 'th' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Origin / Region' },
                            { content: '________________' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Ingredients / Blend' },
                            { content: '________________' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Roast Level / Flavor Profile' },
                            { content: '________________' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Brewing / Preparation Notes' },
                            { content: '________________' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Packaging' },
                            { content: 'Bag, Tin, Box' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Shelf Life' },
                            { content: '______ months' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Awards / Recognition' },
                            { content: '________________' }
                        ]
                    },
                    {
                        cells: [
                            { content: 'Certifications' },
                            { content: 'Organic, Fair Trade, etc.' }
                        ]
                    }
                ]
            }),
            
            // Sensory Notes
            blocks.createBlock('core/heading', {
                content: 'Sensory Notes',
                level: 2
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Appearance / Visual:</strong> Color, foam, leaves, clarity, etc.'
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Aroma / Smell:</strong> Roasted, floral, fruity, earthy, sweet, spicy notes, etc.'
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Taste / Flavor:</strong> Sweetness, bitterness, body, complexity, acidity, specific flavor notes'
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Finish / Aftertaste:</strong> Lingering notes, richness, smoothness, drying sensation'
            }),
            
            // Description / Story
            blocks.createBlock('core/heading', {
                content: 'Description & Story',
                level: 2
            }),
            
            blocks.createBlock('core/paragraph', {
                content: 'Origin story, production process, blend philosophy, heritage, sustainability practices (~150-200 words)'
            }),
            
            // My Verdict
            blocks.createBlock('core/heading', {
                content: 'My Verdict',
                level: 2
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Rating:</strong> ___ / 100 or ___ / 5 stars'
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Pros:</strong><br>• Advantage #1<br>• Advantage #2<br>• Advantage #3'
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Cons:</strong><br>• Drawback #1<br>• Drawback #2'
            }),
            
            blocks.createBlock('core/paragraph', {
                content: '<strong>Recommended For:</strong> Everyday consumption, specialty occasions, gift-giving, pairing with specific foods'
            }),
            
            // Community Reviews
            blocks.createBlock('core/heading', {
                content: 'Community Reviews',
                level: 2
            }),
            
            blocks.createBlock('core/table', {
                hasFixedLayout: false,
                body: [
                    {
                        cells: [
                            { content: 'Reviewer / Alias', tag: 'th' },
                            { content: 'Rating', tag: 'th' },
                            { content: 'Comment', tag: 'th' }
                        ]
                    },
                    {
                        cells: [
                            { content: '________________' },
                            { content: '___ / 5' },
                            { content: '__________________' }
                        ]
                    },
                    {
                        cells: [
                            { content: '________________' },
                            { content: '___ / 5' },
                            { content: '__________________' }
                        ]
                    }
                ]
            }),
            
            // Call to action
            blocks.createBlock('core/buttons', {
                align: 'center',
                buttons: [
                    {
                        text: 'Where to Buy',
                        url: '#',
                        className: 'is-style-fill'
                    }
                ]
            }),
        ];
    }
    
    /**
     * Show notification to user
     */
    function showNotification() {
        try {
            const { createNotice } = wp.data.dispatch('core/notices');
            if (createNotice) {
                createNotice('success', '✅ Curated Review template applied!', {
                    isDismissible: true,
                    type: 'snackbar',
                    actions: []
                });
            } else {
                console.log('✅ Curated Review template applied!');
            }
        } catch (error) {
            console.error('Notification error:', error);
        }
    }
    
    /**
     * Add click listeners to category checkboxes as fallback
     * This catches manual category selection clicks
     */
    function addCategoryCheckboxListeners() {
        try {
            const categoryCheckboxes = document.querySelectorAll('input[type="checkbox"][id*="category"]');
            
            if (categoryCheckboxes.length === 0) {
                console.log('📋 No category checkboxes found in DOM');
                return;
            }
            
            console.log('✓ Found', categoryCheckboxes.length, 'category checkboxes');
            
            categoryCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    console.log('🔔 Category checkbox changed:', checkbox.id);
                    
                    // Check if any Curated Review categories are checked
                    const curatedReviewIds = [24, 25, 26, 27, 28, 29, 30];
                    let hasReviewCategory = false;
                    
                    curatedReviewIds.forEach(id => {
                        const element = document.getElementById('in-category-' + id);
                        if (element && element.checked) {
                            hasReviewCategory = true;
                            console.log('✓ Category', id, 'is checked');
                        }
                    });
                    
                    if (hasReviewCategory && !templateApplied) {
                        templateApplied = true;
                        console.log('✅ Applying template via checkbox listener...');
                        applyTemplate();
                    }
                });
            });
        } catch (error) {
            console.error('Error adding category listeners:', error);
        }
    }
});
