/**
 * Curated Review Template - Alternative Implementation
 * Uses direct DOM observation and REST API
 */

wp.domReady(() => {
    console.log('✓ Curated Review Template (Alt) Script Loaded');
    
    const { dispatch, select } = wp.data;
    
    // Wait a bit for editor to fully load
    setTimeout(() => {
        console.log('Starting category monitoring...');
        monitorCategoryPanel();
    }, 500);
    
    /**
     * Monitor category panel for changes
     */
    function monitorCategoryPanel() {
        try {
            // Find all category checkboxes
            const categoryInputs = document.querySelectorAll('input[type="checkbox"]');
            console.log('Found', categoryInputs.length, 'checkboxes in editor');
            
            // Log their IDs and values
            let categoryCheckboxCount = 0;
            categoryInputs.forEach((input, idx) => {
                if (input.id && input.id.includes('category')) {
                    categoryCheckboxCount++;
                    console.log('  Box', categoryCheckboxCount, ':', input.id, '- Checked:', input.checked);
                }
            });
            
            if (categoryCheckboxCount === 0) {
                console.log('⚠️ No category checkboxes found!');
                console.log('   Opening Chrome DevTools and looking at HTML might help');
                console.log('   The checkboxes might have different IDs');
                return;
            }
            
            console.log('✓ Monitoring', categoryCheckboxCount, 'category checkboxes');
            
            // Add listeners to all category checkboxes
            categoryInputs.forEach(input => {
                if (input.id && input.id.includes('category')) {
                    input.addEventListener('change', () => {
                        console.log('📌 Category changed:', input.id, '- Now checked:', input.checked);
                        checkForCuratedReview();
                    });
                }
            });
            
        } catch (error) {
            console.error('❌ Error monitoring categories:', error);
        }
    }
    
    /**
     * Check if Curated Review category is selected
     */
    function checkForCuratedReview() {
        try {
            // Check specifically for ID 24 (Curated Review)
            // WordPress uses different ID formats, try multiple
            const possibleIds = [
                'in-category-24',      // Classic format
                'category-24',
                'tax-input-category', // Alternative
            ];
            
            console.log('Checking for Curated Review category...');
            
            let isCuratedSelected = false;
            
            // Check checkbox by ID 24
            let checkbox = document.getElementById('in-category-24');
            if (checkbox && checkbox.checked) {
                isCuratedSelected = true;
                console.log('✅ Found: in-category-24 is CHECKED');
            } else {
                console.log('⚠️ in-category-24:', checkbox ? 'unchecked' : 'not found');
            }
            
            // Also check via wp.data
            try {
                const post = select('core/editor').getCurrentPost();
                if (post && post.categories) {
                    console.log('Post categories from wp.data:', post.categories);
                    if (post.categories.includes(24)) {
                        isCuratedSelected = true;
                        console.log('✅ Found: Category 24 in wp.data');
                    }
                }
            } catch (e) {
                console.log('Could not access wp.data post.categories');
            }
            
            if (isCuratedSelected) {
                console.log('');
                console.log('═════════════════════════════════════════');
                console.log('🎯 CURATED REVIEW DETECTED - APPLYING TEMPLATE');
                console.log('═════════════════════════════════════════');
                console.log('');
                applyTemplateNow();
            }
            
        } catch (error) {
            console.error('❌ Error checking for Curated Review:', error);
        }
    }
    
    /**
     * Actually apply the template
     */
    function applyTemplateNow() {
        try {
            const currentBlocks = select('core/block-editor').getBlocks();
            console.log('Current blocks:', currentBlocks.length);
            
            // Check if empty
            const isEmpty = currentBlocks.length === 0 || 
                           (currentBlocks.length === 1 && 
                            currentBlocks[0].name === 'core/paragraph' && 
                            !currentBlocks[0].attributes.content);
            
            if (!isEmpty) {
                console.log('⚠️ Post not empty - not applying template');
                return;
            }
            
            console.log('✓ Post is empty, applying template...');
            
            // Reset blocks
            dispatch('core/block-editor').resetBlocks([]);
            
            // Get template blocks
            const blocks = wp.blocks;
            const templateBlocks = [
                blocks.createBlock('core/heading', {
                    content: 'Header & Product Identity',
                    level: 2
                }),
                blocks.createBlock('core/paragraph', {
                    content: 'Product Name: ___________________'
                }),
                blocks.createBlock('core/paragraph', {
                    content: 'Brand: ___________________'
                }),
                blocks.createBlock('core/heading', {
                    content: 'Quick Facts & Specifications',
                    level: 2
                }),
                blocks.createBlock('core/paragraph', {
                    content: '[Table with specs will be inserted here]'
                }),
                blocks.createBlock('core/heading', {
                    content: 'My Verdict',
                    level: 2
                }),
                blocks.createBlock('core/paragraph', {
                    content: 'Rating: ___ / 5 stars'
                }),
            ];
            
            // Insert blocks
            console.log('Inserting', templateBlocks.length, 'blocks...');
            dispatch('core/block-editor').insertBlocks(templateBlocks, 0, false);
            
            console.log('✅ TEMPLATE APPLIED SUCCESSFULLY!');
            
            // Show notification
            try {
                const { createNotice } = wp.data.dispatch('core/notices');
                if (createNotice) {
                    createNotice('success', '✅ Curated Review template applied!', {
                        isDismissible: true,
                        type: 'snackbar'
                    });
                }
            } catch (e) {
                console.log('Notification dispatch failed (not critical)');
            }
            
        } catch (error) {
            console.error('❌ Error applying template:', error);
            console.error('   ', error.stack);
        }
    }
});
