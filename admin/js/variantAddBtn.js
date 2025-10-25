/**
 * ADD VARIANT FUNCTIONALITY
 * This code adds a new variant row when the "+ Add another variant" button is clicked
 * Works on both product_add.php and product_edit.php pages
 */
document.getElementById('add-variant').addEventListener('click', function() {
    // Get the container that holds all variant rows
    const container = document.getElementById('variants-container');
    
    // Get all existing variant rows currently on the page
    const rows = container.querySelectorAll('.variant-row');
    
    // Get the last row to use as a template for the new row
    const lastRow = rows[rows.length - 1];
    
    // Calculate the index for the new row (if there are 3 rows, newIndex = 3)
    const newIndex = rows.length;

    // Clone (copy) the last row with all its HTML structure and dropdown options
    const newRow = lastRow.cloneNode(true);

    // Loop through all select dropdowns and input fields in the new row
    newRow.querySelectorAll('select, input').forEach(el => {
        // Update the name attribute to use the new index
        // For example: variants[0][color_id] becomes variants[3][color_id]
        const name = el.getAttribute('name');
        const updated = name.replace(/\[\d+\]/, '[' + newIndex + ']');
        el.setAttribute('name', updated);
        
        // Clear the values in the new row so it starts empty
        if (el.classList.contains('variant-id')) {
            // variant-id is a hidden field that stores the database ID
            // New rows don't have a database ID yet, so clear it
            el.value = '';
        } else if (el.tagName === 'SELECT') {
            // For dropdown menus, reset to the first option (usually the placeholder like "Color")
            el.selectedIndex = 0;
        } else {
            // For regular input fields (like stock quantity), clear the value
            el.value = '';
        }
    });

    // Add the new row to the bottom of the container
    container.appendChild(newRow);
});

/**
 * DELETE VARIANT FUNCTIONALITY
 * This code handles deletion of variant rows when the "Delete" button is clicked
 * Only works on product_edit.php page (product_add.php doesn't have delete buttons)
 */
const variantsContainer = document.getElementById('variants-container');

// Check if the variants container exists on this page
if (variantsContainer) {
    // Use event delegation: listen for clicks anywhere in the container
    // This works for both existing rows and newly added rows
    variantsContainer.addEventListener('click', function(e) {
        // Check if the clicked element is a delete button (or inside one)
        const deleteButton = e.target.closest('.delete-variant');
        
        // If a delete button was clicked, proceed with deletion
        if (deleteButton) {
            // Find the parent row that contains this delete button
            const row = deleteButton.closest('.variant-row');
            
            // Find the hidden input field that stores the variant's database ID
            const variantIdInput = row.querySelector('.variant-id');
            
            // Ask the user to confirm they want to delete this variant
            if (!confirm('Are you sure you want to delete this variant?')) {
                // If they click "Cancel", stop here and don't delete anything
                return;
            }

            // If this variant exists in the database (has a variant_id), we need to tell the server to delete it
            if (variantIdInput && variantIdInput.value) {
                // Create a hidden input field to track which variants should be deleted
                let deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'deleted_variants[]'; // The [] makes it an array in PHP
                deleteInput.value = variantIdInput.value; // Store the variant ID to delete
                
                // Add this hidden input to the form so it gets submitted
                document.querySelector('form').appendChild(deleteInput);
            }

            // Remove the row from the page immediately (visual feedback)
            row.remove();

            // Check how many variant rows are left after deletion
            const remainingRows = variantsContainer.querySelectorAll('.variant-row');
            
            // If all rows were deleted, automatically add one empty row
            // This ensures the user always has at least one row to work with
            if (remainingRows.length === 0) {
                // Programmatically click the "Add variant" button
                document.getElementById('add-variant').click();
            }
        }
    });
}