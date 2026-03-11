/**
 * Curated Review Template - Debug Version
 * This version logs extensive debugging info
 */

wp.domReady(() => {
    console.log('=== CURATED REVIEW TEMPLATE DEBUG ===');
    console.log('Time:', new Date().toLocaleTimeString());
    
    const { subscribe } = wp.data;
    const { dispatch, select } = wp.data;
    
    // Check if block editor available
    if (!select || !dispatch) {
        console.error('❌ Block Editor not available!');
        return;
    }
    
    console.log('✓ Block Editor API available');
    
    let templateApplied = false;
    let changeCount = 0;
    
    // Subscribe to ALL store changes
    const unsubscribe = subscribe(() => {
        changeCount++;
        
        if (changeCount % 10 === 0) { // Log every 10th change to reduce spam
            console.log('↻ Store changed (' + changeCount + ' total)');
        }
        
        try {
            const post = select('core/editor').getCurrentPost();
            
            if (!post) {
                if (changeCount === 1) console.log('⚠ No post yet');
                return;
            }
            
            // Log post data
            if (changeCount === 1 || changeCount % 50 === 0) {
                console.log('📄 Post Data:');
                console.log('   ID:', post.id);
                console.log('   Title:', post.title);
                console.log('   Status:', post.status);
                console.log('   Categories:', post.categories);
                console.log('   Type:', typeof post.categories, Array.isArray(post.categories) ? '(array)' : '(not array)');
            }
            
            // Get categories
            const categories = post.categories || [];
            
            if (categories.length > 0 && changeCount % 10 === 0) {
                console.log('✓ Categories found:', categories);
            }
            
            // Check if Curated Review is selected (ID 24)
            const hasCuratedReview = categories.includes(24);
            
            if (hasCuratedReview && changeCount % 10 === 0) {
                console.log('🎯 CURATED REVIEW CATEGORY DETECTED! ID 24 found');
            }
            
            if (hasCuratedReview && !templateApplied) {
                console.log('');
                console.log('═══════════════════════════════════════');
                console.log('✅ APPLYING TEMPLATE NOW!');
                console.log('═══════════════════════════════════════');
                console.log('');
                
                templateApplied = true;
                applyTemplate();
            }
            
        } catch (error) {
            console.error('❌ Error:', error.message);
        }
    });
    
    function applyTemplate() {
        try {
            console.log('🔧 Getting current blocks...');
            const currentBlocks = select('core/block-editor').getBlocks();
            console.log('   Current blocks:', currentBlocks.length);
            
            currentBlocks.forEach((block, idx) => {
                console.log('   Block ' + idx + ':', block.name);
            });
            
            const isEmpty = !currentBlocks || currentBlocks.length === 0 || 
                           (currentBlocks.length === 1 && 
                            (currentBlocks[0].name === 'core/paragraph' || 
                             currentBlocks[0].name === 'core/image'));
            
            console.log('📝 Is empty?', isEmpty);
            
            if (isEmpty) {
                console.log('🔄 Clearing blocks...');
                dispatch('core/block-editor').resetBlocks([]);
                
                console.log('📋 Creating template blocks...');
                const template = getCuratedReviewTemplate();
                console.log('   Created', template.length, 'blocks');
                
                console.log('📌 Inserting blocks...');
                dispatch('core/block-editor').insertBlocks(template, 0, false);
                
                console.log('✅ Template applied!');
                showNotification();
            } else {
                console.log('⚠️ Post not empty - skipping template');
                console.log('   (Create a new post to see template)');
            }
        } catch (error) {
            console.error('❌ Error applying template:', error);
            console.error('   Stack:', error.stack);
        }
    }
    
    function showNotification() {
        try {
            const { createNotice } = wp.data.dispatch('core/notices');
            if (createNotice) {
                createNotice('success', '✅ Curated Review template applied!', {
                    isDismissible: true,
                    type: 'snackbar'
                });
                console.log('🔔 Notification shown');
            }
        } catch (error) {
            console.error('Notification error:', error);
        }
    }
    
    // Get template
    function getCuratedReviewTemplate() {
        const blocks = wp.blocks;
        return [
            blocks.createBlock('core/heading', {
                content: 'Header & Product Identity',
                level: 2
            }),
            blocks.createBlock('core/paragraph', {
                content: '<strong>Product Name:</strong> ________________'
            }),
            blocks.createBlock('core/heading', {
                content: 'Product Details',
                level: 2
            }),
            blocks.createBlock('core/paragraph', {
                content: '[Insert product image placeholder]'
            }),
        ];
    }
    
    console.log('✓ Debug listener ready');
    console.log('📋 Now go select "Curated Review" category to test');
    console.log('');
});
