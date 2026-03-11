/**
 * DIAGNOSTIC SCRIPT - Find exact checkbox IDs
 * This will show us what WordPress actually calls the checkboxes
 */

wp.domReady(() => {
    console.log('===========================================');
    console.log('DIAGNOSTIC: Finding all checkbox IDs');
    console.log('===========================================');
    console.log('');
    
    // Find ALL checkboxes
    const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    console.log('Total checkboxes found:', allCheckboxes.length);
    console.log('');
    
    if (allCheckboxes.length === 0) {
        console.log('❌ No checkboxes at all?');
        return;
    }
    
    console.log('ALL CHECKBOX IDs:');
    console.log('─────────────────────────────────────');
    
    allCheckboxes.forEach((checkbox, idx) => {
        console.log((idx + 1) + '. ID: ' + (checkbox.id || '(NO ID)'));
        console.log('   Name: ' + (checkbox.name || '(NO NAME)'));
        console.log('   Value: ' + (checkbox.value || '(NO VALUE)'));
        console.log('   Checked: ' + checkbox.checked);
        console.log('');
    });
    
    console.log('─────────────────────────────────────');
    console.log('');
    console.log('LOOKING FOR CATEGORY-RELATED CHECKBOXES:');
    console.log('─────────────────────────────────────');
    
    allCheckboxes.forEach((checkbox, idx) => {
        const id = (checkbox.id || '').toLowerCase();
        const name = (checkbox.name || '').toLowerCase();
        const value = (checkbox.value || '').toString();
        
        // Check if it might be category-related
        if (id.includes('categor') || id.includes('tax') || name.includes('categor') || name.includes('tax')) {
            console.log('Found possible category checkbox:');
            console.log('  ID: ' + checkbox.id);
            console.log('  Name: ' + checkbox.name);
            console.log('  Value: ' + checkbox.value);
            console.log('  Checked: ' + checkbox.checked);
            console.log('');
        }
    });
    
    // Also check for labels that mention categories
    console.log('─────────────────────────────────────');
    console.log('LOOKING FOR CATEGORY LABELS:');
    console.log('─────────────────────────────────────');
    
    const allLabels = document.querySelectorAll('label');
    console.log('Found', allLabels.length, 'labels');
    console.log('');
    
    allLabels.forEach((label, idx) => {
        const text = label.textContent.toLowerCase();
        if (text.includes('curated') || text.includes('category') || text.includes('fragrance')) {
            console.log('Label #' + idx + ': ' + label.textContent);
            console.log('  For ID: ' + label.htmlFor);
            const linkedCheckbox = document.getElementById(label.htmlFor);
            if (linkedCheckbox) {
                console.log('  Linked checkbox value: ' + linkedCheckbox.value);
            }
            console.log('');
        }
    });
    
    console.log('═════════════════════════════════════════');
    console.log('NEXT STEP: Share this console output!');
    console.log('═════════════════════════════════════════');
});
