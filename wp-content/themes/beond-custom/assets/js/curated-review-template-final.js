/**
 * Curated Review Template - Final Working Version
 * Uses wp.data properly to detect category changes
 */

wp.domReady(() => {
    console.log('✓ Curated Review Template Loaded');
    
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
            // Get the editor context
            const editorSelect = select('core/editor');
            if (!editorSelect) return;
            
            // Get current post data
            const post = editorSelect.getCurrentPost();
            if (!post) return;
            
            // Get post meta for categories
            // New Block Editor stores them in meta or post data
            let categories = [];
            
            // Method 1: Direct categories array (most common)
            if (post.categories && Array.isArray(post.categories)) {
                categories = post.categories;
            }
            
            // Method 2: Check if saving/updating changes the structure
            // Sometimes categories are in _links
            if (categories.length === 0 && post._links && post._links['wp:term']) {
                post._links['wp:term'].forEach(term => {
                    if (term.taxonomy === 'category') {
                        categories.push(term.id);
                    }
                });
            }
            
            // Create a string to compare changes
            const categoryString = JSON.stringify(categories.sort());
            
            // Only process if categories changed
            if (categoryString === lastCategoryString) {
                return;
            }
            
            lastCategoryString = categoryString;
            
            // Check if Curated Review category (24) is selected
            const hasCuratedReview = categories.includes(24);
            
            if (hasCuratedReview && !templateApplied) {
                console.log('✅ Curated Review category detected! Categories:', categories);
                templateApplied = true;
                applyTemplate();
            }
            
            if (!hasCuratedReview && templateApplied) {
                templateApplied = false;
                console.log('Category removed, template flag reset');
            }
            
        } catch (error) {
            console.error('Error in template subscription:', error);
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
                console.error('❌ Block editor dispatch/select not available');
                return;
            }
            
            // Get current blocks
            const blocks = editorSelect.getBlocks();
            console.log('Current blocks:', blocks.length);
            
            // Check if post is empty (has no content or only empty default blocks)
            const isEmpty = !blocks || blocks.length === 0 || blocks.every(block => 
                block.name === 'core/paragraph' && !block.attributes.content
            );
            
            if (!isEmpty) {
                console.log('⚠️ Post not empty, skipping template');
                return;
            }
            
            console.log('📝 Post empty, applying template...');
            
            // Create template blocks
            const templateBlocks = createTemplateBlocks();
            
            // Clear existing and insert new blocks
            editorDispatch.resetBlocks([]);
            editorDispatch.insertBlocks(templateBlocks, 0, false);
            
            console.log('✅ Template applied! Blocks inserted:', templateBlocks.length);
            
            // Show notification
            showNotification();
            
        } catch (error) {
            console.error('❌ Error applying template:', error);
            console.error('Stack:', error.stack);
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
                content: '<strong>Product Name:</strong> ________________<br><strong>Brand:</strong> ________________<br><strong>Type:</strong> ________________<br><strong>Price:</strong> ________________<br><strong>Weight / Pack Size:</strong> ________________'
            }),
            createBlock('core/heading', {
                content: 'Visual & Media',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '[Insert product image or gallery]'
            }),
            createBlock('core/heading', {
                content: 'Quick Facts & Specifications',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<strong>Origin / Region:</strong> ________________<br><strong>Ingredients / Blend:</strong> ________________<br><strong>Special Features:</strong> ________________'
            }),
            createBlock('core/heading', {
                content: 'Sensory Notes',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<strong>Appearance:</strong> ________________<br><strong>Aroma / Smell:</strong> ________________<br><strong>Taste / Flavor:</strong> ________________<br><strong>Finish / Aftertaste:</strong> ________________'
            }),
            createBlock('core/heading', {
                content: 'Description & Story',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: 'Write origin story, production process, and heritage here (~150-200 words)...'
            }),
            createBlock('core/heading', {
                content: 'My Verdict',
                level: 2
            }),
            createBlock('core/paragraph', {
                content: '<strong>Rating:</strong> ___ / 5 stars<br><strong>Pros:</strong> • ________________ • ________________<br><strong>Cons:</strong> • ________________<br><strong>Recommended For:</strong> ________________'
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
                console.log('Notification displayed');
            }
        } catch (e) {
            console.log('Notification not available (not critical)');
        }
    }
    
    console.log('✓ Template listener ready - select Curated Review category to test');
});
