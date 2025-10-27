/**
 * Product Variants Selection Handler
 * Works with products that may have:
 * - Color + Size
 * - Only Color
 * - Only Size
 * - No variants at all
 */

function initializeProductVariants(variants, productId) {
    // Cache DOM elements
    const colorOptions = document.querySelectorAll('.color-option');
    const sizeOptions = document.querySelectorAll('.size-option');
    const priceDisplay = document.getElementById('price');
    const qtyInput = document.getElementById('qty_' + productId);
    const addToCartBtn = document.getElementById('addToCartBtn');
    const stockInfo = document.getElementById('stockInfo');
    const stockBadge = document.getElementById('stockBadge');
    const selectedPriceInput = document.getElementById('selectedPrice');
    const selectedVariantIdInput = document.getElementById('selectedVariantId');
    const selectedSkuInput = document.getElementById('selectedSku');
    const selectedColorName = document.getElementById('selectedColorName');
    const selectedSizeName = document.getElementById('selectedSizeName');

    // Track selected options
    let selectedColor = null;
    let selectedSize = null;

    // ---------------------------
    // Refresh color availability
    // ---------------------------
    function refreshColorAvailability() {
        colorOptions.forEach(option => {
            const color = option.dataset.color;
            const match = variants.find(v =>
                v.color_name === color &&
                v.available === 'Yes' &&
                parseInt(v.stock_qty) > 0
            );

            if (match) {
                option.classList.remove('disabled');
                const input = option.querySelector('input');
                if (input) input.disabled = false;
            } else {
                option.classList.add('disabled');
                option.classList.remove('selected');
                const input = option.querySelector('input');
                if (input) {
                    input.disabled = true;
                    input.checked = false;
                }
                if (selectedColor === color) {
                    selectedColor = null;
                    if (selectedColorName) selectedColorName.textContent = 'Select a color';
                }
            }
        });
    }

    // ---------------------------
    // Refresh size availability
    // ---------------------------
    function refreshSizeAvailability() {
        sizeOptions.forEach(option => {
            const size = option.dataset.size;
            const match = variants.find(v =>
                (!selectedColor || v.color_name === selectedColor) &&
                v.size_name === size &&
                v.available === 'Yes' &&
                parseInt(v.stock_qty) > 0
            );

            if (match) {
                option.classList.remove('disabled');
                const input = option.querySelector('input');
                if (input) input.disabled = false;
            } else {
                option.classList.add('disabled');
                option.classList.remove('selected');
                const input = option.querySelector('input');
                if (input) {
                    input.disabled = true;
                    input.checked = false;
                }
                if (selectedSize === size) {
                    selectedSize = null;
                    if (selectedSizeName) selectedSizeName.textContent = 'Select a size';
                }
            }
        });
    }

    // ---------------------------
    // Update product details
    // ---------------------------
    function updateVariant() {
        const matchingVariant = variants.find(v => {
            const colorMatch = !selectedColor || v.color_name === selectedColor;
            const sizeMatch = !selectedSize || v.size_name === selectedSize;
            return colorMatch && sizeMatch;
        });

        // Flexible condition: only require what exists
        if (matchingVariant &&
            (!colorOptions.length || selectedColor) &&
            (!sizeOptions.length || selectedSize)) {

            const price = matchingVariant.sale_price && matchingVariant.sale_price > 0
                ? matchingVariant.sale_price
                : matchingVariant.price;
            const displayPrice = parseFloat(price).toFixed(2);

            if (matchingVariant.sale_price && matchingVariant.sale_price > 0) {
                priceDisplay.innerHTML =
                    `<span class="original-price">$ ${parseFloat(matchingVariant.price).toFixed(2)}</span> 
                     <span class="sale-price">$ ${displayPrice}</span> CAD`;
            } else {
                priceDisplay.innerHTML = `<span style="font-weight: bold;">$ ${displayPrice}</span> CAD`;
            }

            selectedPriceInput.value = price;
            selectedVariantIdInput.value = matchingVariant.variant_id;
            selectedSkuInput.value = matchingVariant.sku;

            const stockQty = parseInt(matchingVariant.stock_qty);
            stockInfo.style.display = 'block';

            if (stockQty > 10) {
                stockBadge.className = 'stock-badge in-stock';
                stockBadge.textContent = `In Stock (${stockQty} available)`;
            } else if (stockQty > 0) {
                stockBadge.className = 'stock-badge low-stock';
                stockBadge.textContent = `Low Stock (Only ${stockQty} left)`;
            } else {
                stockBadge.className = 'stock-badge out-of-stock';
                stockBadge.textContent = 'Out of Stock';
            }

            qtyInput.max = stockQty;

            if (stockQty < 1) {
                addToCartBtn.disabled = true;
                addToCartBtn.textContent = 'Out of Stock';
            } else {
                addToCartBtn.disabled = false;
                addToCartBtn.textContent = 'Add to cart';
            }
        } else {
            stockInfo.style.display = 'none';
            selectedPriceInput.value = '';
            selectedVariantIdInput.value = '';
            selectedSkuInput.value = '';
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Select options to continue';
        }
    }

    // ---------------------------
    // Event: Color click
    // ---------------------------
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            colorOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedColor = this.dataset.color;
            if (selectedColorName) selectedColorName.textContent = selectedColor;
            const input = this.querySelector('input');
            if (input) input.checked = true;
            refreshSizeAvailability();
            updateVariant();
        });
    });

    // ---------------------------
    // Event: Size click
    // ---------------------------
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            sizeOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedSize = this.dataset.size;
            if (selectedSizeName) selectedSizeName.textContent = selectedSize;
            const input = this.querySelector('input');
            if (input) input.checked = true;
            updateVariant();
        });
    });

    // ---------------------------
    // Form submission validation
    // ---------------------------
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            if (!selectedVariantIdInput.value) {
                e.preventDefault();
                alert('Please select available product options');
            }
        });
    }

    // ---------------------------
    // Initial setup
    // ---------------------------
    refreshColorAvailability();
    refreshSizeAvailability();
    updateVariant();
}

// ---------------------------
// Wait for page load
// ---------------------------
document.addEventListener('DOMContentLoaded', function() {
    if (typeof productVariantsData !== 'undefined' && typeof productIdData !== 'undefined') {
        initializeProductVariants(productVariantsData, productIdData);
    }
});
