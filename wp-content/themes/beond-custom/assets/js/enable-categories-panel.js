/**
 * Enable Categories Panel in Block Editor
 * Ensures category panel is visible for all users, especially authors
 */
(function() {
    'use strict';
    
    // Wait for the editor to be ready
    wp.domReady(function() {
        // Get the editor settings
        if (wp.data && wp.data.select && wp.data.dispatch) {
            const editorStore = wp.data.select('core/edit-post');
            const editorDispatch = wp.data.dispatch('core/edit-post');
            
            if (editorStore && editorDispatch) {
                // Check if categories panel is enabled
                const isCategoriesEnabled = editorStore.isEditorPanelEnabled('taxonomy-panel-category');
                
                // If not enabled, enable it
                if (!isCategoriesEnabled) {
                    console.log('Enabling categories panel...');
                    editorDispatch.toggleEditorPanelEnabled('taxonomy-panel-category');
                }
                
                // Also enable post tags panel if hidden
                const isTagsEnabled = editorStore.isEditorPanelEnabled('taxonomy-panel-post_tag');
                if (!isTagsEnabled) {
                    console.log('Enabling tags panel...');
                    editorDispatch.toggleEditorPanelEnabled('taxonomy-panel-post_tag');
                }
                
                // Open the categories panel by default
                const isCategoriesOpened = editorStore.isEditorPanelOpened('taxonomy-panel-category');
                if (!isCategoriesOpened) {
                    editorDispatch.toggleEditorPanelOpened('taxonomy-panel-category');
                }
            }
        }
    });
})();
