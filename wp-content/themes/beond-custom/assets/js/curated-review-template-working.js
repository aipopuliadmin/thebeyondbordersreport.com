/**
 * Curated Review Template - WORKING VERSION
 * Uses getEditedPostAttribute('categories') which actually contains the data
 */

wp.domReady(() => {
    console.log('✅ Curated Review Template ACTIVE');
    
    const { select, subscribe } = wp.data;
    
    if (!select || !subscribe) {
        console.error('❌ WordPress data API not available');
        return;
    }
    
    let templateApplied = false;
    let lastCategoryString = '';
    
    // Subscribe to editor store changes
    const unsubscribe = subscribe(() => {
        try {
            const editorSelect = select('core/editor');
            if (!editorSelect) return;
            
            // THIS IS THE KEY: Use getEditedPostAttribute instead of getCurrentPost().categories
            const categories = editorSelect.getEditedPostAttribute('categories');
            
            // Skip if categories is not an array
            if (!Array.isArray(categories)) {
                return;
            }
            
            // Create string to detect changes
            const categoryString = JSON.stringify(categories.sort());
            
            // Only process if categories changed
            if (categoryString === lastCategoryString) {
                return;
            }
            
            lastCategoryString = categoryString;
            
            // Check if Curated Review category (24) is selected
            const hasCuratedReview = categories.includes(24);
            
            if (hasCuratedReview && !templateApplied) {
                console.log('');
                console.log('═══════════════════════════════════════════════');
                console.log('✅ CURATED REVIEW DETECTED!');
                console.log('   Categories:', categories);
                console.log('═══════════════════════════════════════════════');
                console.log('');
                
                templateApplied = true;
                applyTemplate();
            }
            
            if (!hasCuratedReview && templateApplied) {
                templateApplied = false;
                console.log('📌 Category removed, template flag reset');
            }
            
        } catch (error) {
            console.error('❌ Error:', error.message);
        }
    });
    
    /**
     * Apply template blocks to editor
     */
    function applyTemplate() {
        try {
            const { dispatch, select: sel } = wp.data;
            const editorDispatch = dispatch('core/block-editor');
            const editorSelect = sel('core/block-editor');
            
            if (!editorDispatch || !editorSelect) {
                console.error('❌ Block editor not available');
                return;
            }
            
            // Get current blocks
            const blocks = editorSelect.getBlocks();
            console.log('📦 Current blocks:', blocks.length);
            
            // Check if post is empty
            const isEmpty = !blocks || blocks.length === 0 || blocks.every(block => 
                block.name === 'core/paragraph' && !block.attributes.content
            );
            
            if (!isEmpty) {
                console.log('⚠️ Post not empty - asking user for confirmation...');
                
                // Ask user if they want to replace existing content
                const confirmReplace = confirm(
                    '⚠️ This post already has content.\n\n' +
                    'Do you want to REPLACE it with the Curated Review template?\n\n' +
                    '• Click OK to replace existing content with template\n' +
                    '• Click Cancel to keep your existing content'
                );
                
                if (!confirmReplace) {
                    console.log('❌ User cancelled template application');
                    console.log('   Keeping existing content');
                    return;
                }
                
                console.log('✅ User confirmed - replacing content with template');
            } else {
                console.log('✓ Post is empty, applying template...');
            }
            
            // Create template blocks
            const templateBlocks = createTemplateBlocks();
            
            // Replace all blocks with template blocks
            editorDispatch.resetBlocks(templateBlocks);
            
            console.log('');
            console.log('🎉 TEMPLATE APPLIED SUCCESSFULLY!');
            console.log('   Inserted', templateBlocks.length, 'blocks');
            console.log('');
            
            showNotification();
            
        } catch (error) {
            console.error('❌ Error applying template:', error);
        }
    }
    
    /**
     * Create template blocks
     */
    function createTemplateBlocks() {
        const { createBlock } = wp.blocks;
        
        return [
            createBlock('core/heading', {
                content: 'Header & Product Identity',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<strong>Product Name:</strong> ________________<br><strong>Brand:</strong> ________________<br><strong>Type:</strong> Coffee / Tea / Gourmet Item<br><strong>Price:</strong> USD ___ / Local Currency ___<br><strong>Weight / Pack Size:</strong> _____ g'
            }),
            createBlock('core/heading', {
                content: 'Visual & Media',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<em>[Insert product image - packaging, prepared drink, or serving suggestion]</em>'
            }),
            createBlock('core/heading', {
                content: 'Quick Facts & Specifications',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<strong>Origin / Region:</strong> ________________<br><strong>Ingredients / Blend:</strong> ________________<br><strong>Roast Level / Flavor Profile:</strong> ________________<br><strong>Brewing / Preparation Notes:</strong> ________________<br><strong>Packaging:</strong> Bag / Tin / Box<br><strong>Shelf Life:</strong> ______ months<br><strong>Awards / Recognition:</strong> ________________'
            }),
            createBlock('core/heading', {
                content: 'Sensory Notes',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<strong>Appearance / Visual:</strong> Color, foam, leaves, clarity, etc.<br><strong>Aroma / Smell:</strong> Roasted, floral, fruity, earthy, sweet, spicy notes<br><strong>Taste / Flavor:</strong> Sweetness, bitterness, body, complexity<br><strong>Finish / Aftertaste:</strong> Lingering notes, richness, smoothness'
            }),
            createBlock('core/heading', {
                content: 'Description & Story',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: 'Write the origin story, blend philosophy, production process, heritage, sustainability practices (~150-200 words)...'
            }),
            createBlock('core/heading', {
                content: 'My Verdict',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<strong>Rating:</strong> ___ / 100 or ___ / 5 stars<br><br><strong>Pros:</strong><br>• ________________<br>• ________________<br>• ________________<br><br><strong>Cons:</strong><br>• ________________<br>• ________________<br><br><strong>Recommended For:</strong> Everyday, Specialty, Gift, Pairing'
            }),
            createBlock('core/heading', {
                content: 'Community Reviews',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<em>(Optional: Add community feedback or testimonials here)</em>'
            }),
            createBlock('core/heading', {
                content: 'Where to Buy',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<em>[Add purchase links or availability information]</em>'
            })
        ];
    }
    
    /**
     * Show notification
     */
    function showNotification() {
        try {
            const { createNotice } = wp.data.dispatch('core/notices');
            if (createNotice) {
                createNotice('success', '✅ Curated Review template applied!', {
                    isDismissible: true,
                    type: 'snackbar'
                });
            }
        } catch (e) {
            console.log('(Notification not displayed, but template was applied)');
        }
    }
    
    console.log('✓ Listener ready - Select "Curated Review" category to apply template');
});
