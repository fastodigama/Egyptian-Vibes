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
    
    // Update delete button visibility after adding a row
    updateDeleteButtons();
});

/**
 * DELETE VARIANT FUNCTIONALITY
 * This code handles deletion of variant rows when the "Delete" button is clicked
 * Works on both product_add.php and product_edit.php pages
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
            
            // Count how many rows exist before deletion
            const remainingRows = variantsContainer.querySelectorAll('.variant-row');
            
            // RULE: Must keep at least 1 variant row
            // Don't allow deletion if this is the last row
            if (remainingRows.length === 1) {
                alert('You must have at least one variant row. Cannot delete the last row.');
                return; // Stop here, don't delete
            }
            
            // Ask the user to confirm they want to delete this variant
            if (!confirm('Are you sure you want to delete this variant?')) {
                // If they click "Cancel", stop here and don't delete anything
                return;
            }

            // Find the hidden input field that stores the variant's database ID
            // This only exists on product_edit.php, not on product_add.php
            const variantIdInput = row.querySelector('.variant-id');

            // If this variant exists in the database (has a variant_id), we need to tell the server to delete it
            // This only applies to product_edit.php where variants are already saved in the database
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
            
            // Update delete button visibility after removing a row
            updateDeleteButtons();
        }
    });
}

/**
 * UPDATE DELETE BUTTON VISIBILITY
 * Hide delete buttons when only 1 row exists
 * Show delete buttons when 2 or more rows exist
 */
function updateDeleteButtons() {
    const rows = document.querySelectorAll('.variant-row');
    const deleteButtons = document.querySelectorAll('.delete-variant');
    
    // If only 1 row exists, hide all delete buttons
    if (rows.length === 1) {
        deleteButtons.forEach(btn => {
            btn.style.visibility = 'hidden'; // Hide but keep space
        });
    } else {
        // If 2+ rows exist, show all delete buttons
        deleteButtons.forEach(btn => {
            btn.style.visibility = 'visible'; // Show buttons
        });
    }
}

// Run once when page loads to set initial button visibility
document.addEventListener('DOMContentLoaded', function() {
    updateDeleteButtons();
});