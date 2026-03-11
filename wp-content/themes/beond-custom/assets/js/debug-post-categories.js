/**
 * DEBUG: Show exactly what WordPress provides in post object
 */

wp.domReady(() => {
    console.log('===========================================');
    console.log('DEBUG: Checking post.categories data');
    console.log('===========================================');
    
    const { select, subscribe } = wp.data;
    
    let checkCount = 0;
    
    const unsubscribe = subscribe(() => {
        checkCount++;
        
        if (checkCount % 20 === 0) { // Log every 20 updates to avoid spam
            try {
                const post = select('core/editor').getCurrentPost();
                
                console.log('');
                console.log('─── Post Data (check #' + checkCount + ') ───');
                console.log('Post ID:', post.id);
                console.log('Post Title:', post.title);
                console.log('');
                console.log('POST.CATEGORIES:');
                console.log('  Type:', typeof post.categories);
                console.log('  Value:', post.categories);
                console.log('  Is Array:', Array.isArray(post.categories));
                if (Array.isArray(post.categories)) {
                    console.log('  Length:', post.categories.length);
                    console.log('  Contents:', JSON.stringify(post.categories));
                    console.log('  Includes 24?', post.categories.includes(24));
                }
                console.log('');
                
                // Also check using getEditedPostAttribute
                const categories = select('core/editor').getEditedPostAttribute('categories');
                console.log('EDITED POST ATTRIBUTE (categories):');
                console.log('  Type:', typeof categories);
                console.log('  Value:', categories);
                console.log('  Is Array:', Array.isArray(categories));
                if (Array.isArray(categories)) {
                    console.log('  Length:', categories.length);
                    console.log('  Contents:', JSON.stringify(categories));
                    console.log('  Includes 24?', categories.includes(24));
                }
                console.log('');
                
                // Check terms
                const terms = select('core/editor').getEditedPostAttribute('terms');
                console.log('TERMS OBJECT:');
                console.log('  Type:', typeof terms);
                console.log('  Value:', terms);
                if (terms && terms.category) {
                    console.log('  terms.category:', terms.category);
                    console.log('  Includes 24?', terms.category.includes(24));
                }
                
                console.log('─────────────────────────────────────────');
                console.log('');
                
            } catch (error) {
                console.error('Error:', error.message);
            }
        }
    });
    
    console.log('✓ Debug listener active');
    console.log('📋 Now CHECK or UNCHECK "Curated Review" category');
    console.log('📋 Watch for "Post Data" logs every 20 updates');
    console.log('');
});
