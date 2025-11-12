// view-cart.js
document.addEventListener('DOMContentLoaded', function () {
    let itemToRemove = null;

    // =========================
    // Quantity buttons
    // =========================
    document.querySelectorAll('.qty-decrease').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const input = document.getElementById('qty-' + id);
            if (!input) return;

            const currentVal = parseInt(input.value);
            if (currentVal > 1) {
                input.value = currentVal - 1;
                updateQuantity(id, currentVal - 1);
            }
        });
    });

    document.querySelectorAll('.qty-increase').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const input = document.getElementById('qty-' + id);
            if (!input) return;

            const currentVal = parseInt(input.value);
            if (currentVal < 99) {
                input.value = currentVal + 1;
                updateQuantity(id, currentVal + 1);
            }
        });
    });

    // =========================
    // Manual quantity input
    // =========================
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function () {
            const id = this.dataset.id;
            const newQty = parseInt(this.value);
            if (newQty >= 1 && newQty <= 99) {
                updateQuantity(id, newQty);
            } else {
                this.value = 1;
                updateQuantity(id, 1);
            }
        });
    });

    // =========================
    // Remove item flow
    // =========================
    const modal = document.getElementById('remove-modal');
    const confirmBtn = document.getElementById('confirm-remove');
    const cancelBtn = document.getElementById('cancel-remove');

    // Open modal
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            itemToRemove = this.dataset.id;
            if (modal) modal.style.display = 'flex';
        });
    });

    // Confirm removal
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (itemToRemove) {
                removeItem(itemToRemove);
                itemToRemove = null;
            }
            if (modal) modal.style.display = 'none';
        });
    }

    // Cancel removal
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            itemToRemove = null;
            if (modal) modal.style.display = 'none';
        });
    }

    // Click outside modal closes it
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            itemToRemove = null;
        }
    });

    // =========================
    // Update quantity AJAX
    // =========================
    function updateQuantity(productId, quantity) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('new_quantity', quantity);
        formData.append('update_qty', '1');

        fetch('view_cart.php?ajax=1', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) updateCartTotals();
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }

    // =========================
    // Remove item AJAX
    // =========================
    function removeItem(productId) {
        fetch('view_cart.php?ajax=1&remove=' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartItem = document.querySelector(`.cart-item[data-item-id="${productId}"]`);
                if (cartItem) {
                    cartItem.style.opacity = '0';
                    cartItem.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        cartItem.remove();
                        updateCartTotals();
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }

    // =========================
    // Update totals live
    // =========================
    function updateCartTotals() {
        let subtotal = 0;
        const cartItems = document.querySelectorAll('.cart-item');

        cartItems.forEach(item => {
            const itemId = item.dataset.itemId;
            const qtyInput = document.getElementById('qty-' + itemId);
            if (!qtyInput) return;

            const quantity = parseInt(qtyInput.value);
            const priceText = item.querySelector('.item-price').textContent;
            const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));

            const itemTotal = price * quantity;
            subtotal += itemTotal;

            const totalEl = item.querySelector('.item-total strong');
            if (totalEl) totalEl.textContent = '$' + itemTotal.toFixed(2);
        });

        const shipping = 15.00;
        const taxRate = 0.13;
        const tax = subtotal * taxRate;
        const grandTotal = subtotal + shipping + tax;

        const subtotalEl = document.getElementById('subtotal');
        if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toFixed(2);

        const taxEl = document.getElementById('tax');
        if (taxEl) taxEl.textContent = '$' + tax.toFixed(2);

        const grandTotalEl = document.getElementById('grand-total');
        if (grandTotalEl) grandTotalEl.innerHTML = '<strong>$' + grandTotal.toFixed(2) + '</strong>';

        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = cartItems.length + ' item(s)';
        }
    }
});
