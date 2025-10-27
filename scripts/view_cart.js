// view-cart.js
document.addEventListener('DOMContentLoaded', function () {
    let itemToRemove = null; // holds id of item to remove

    // Decrease quantity button
    document.querySelectorAll('.qty-decrease').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id; // get product id
            const input = document.getElementById('qty-' + id); // find qty input
            if (!input) return; // safety check

            const currentVal = parseInt(input.value); // current qty
            if (currentVal > 1) { // stop at 1
                input.value = currentVal - 1; // update input
                updateQuantity(id, currentVal - 1); // send to server
            }
        });
    });

    // Increase quantity button
    document.querySelectorAll('.qty-increase').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id; // product id
            const input = document.getElementById('qty-' + id); // qty input
            if (!input) return; // safety check

            const currentVal = parseInt(input.value); // current qty
            if (currentVal < 99) { // max 99
                input.value = currentVal + 1; // update input
                updateQuantity(id, currentVal + 1); // send to server
            }
        });
    });

    // Manual quantity input
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function () {
            const id = this.dataset.id; // product id
            const newQty = parseInt(this.value); // new qty
            if (newQty >= 1 && newQty <= 99) { // valid range
                updateQuantity(id, newQty); // send to server
            } else {
                // reset to 1 if invalid
                this.value = 1;
                updateQuantity(id, 1);
            }
        });
    });

    // Remove button (open modal)
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function () {
            itemToRemove = this.dataset.id; // store id
            const modal = document.getElementById('remove-modal');
            if (modal) modal.style.display = 'flex'; // show modal
        });
    });

    // Confirm remove
    const confirmBtn = document.getElementById('confirm-remove');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (itemToRemove) { // if set
                removeItem(itemToRemove); // remove via AJAX
                const modal = document.getElementById('remove-modal');
                if (modal) modal.style.display = 'none'; // hide modal
            }
        });
    }

    // Cancel remove
    const cancelBtn = document.getElementById('cancel-remove');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            itemToRemove = null; // reset
            const modal = document.getElementById('remove-modal');
            if (modal) modal.style.display = 'none'; // hide modal
        });
    }

    // Update quantity AJAX
    function updateQuantity(productId, quantity) {
        const formData = new FormData(); // form data object
        formData.append('product_id', productId); // add product id
        formData.append('new_quantity', quantity); // add new qty
        formData.append('update_qty', '1'); // flag

        fetch('view_cart.php?ajax=1', { // send request
            method: 'POST',
            body: formData
        })
            .then(response => response.json()) // parse JSON
            .then(data => {
                if (data.success) updateCartTotals(); // refresh totals
            })
            .catch(error => {
                console.error('Error:', error); // log error
                location.reload(); // fallback reload
            });
    }

    // Remove item AJAX
    function removeItem(productId) {
        fetch('view_cart.php?ajax=1&remove=' + productId) // send request
            .then(response => response.json()) // parse JSON
            .then(data => {
                if (data.success) { // if ok
                    const cartItem = document.querySelector(`.cart-item[data-item-id="${productId}"]`); // find DOM item
                    if (cartItem) {
                        cartItem.style.opacity = '0'; // fade out
                        cartItem.style.transition = 'opacity 0.3s'; // smooth
                        setTimeout(() => { // after fade
                            cartItem.remove(); // remove from DOM
                            updateCartTotals(); // refresh totals
                            if (document.querySelectorAll('.cart-item').length === 0) { // if empty
                                location.reload(); // reload page
                            }
                        }, 300);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error); // log error
                location.reload(); // fallback reload
            });
    }

    // Recalculate totals
    function updateCartTotals() {
        let subtotal = 0; // start subtotal
        const cartItems = document.querySelectorAll('.cart-item'); // all items

        cartItems.forEach(item => {
            const itemId = item.dataset.itemId; // id
            const qtyInput = document.getElementById('qty-' + itemId); // qty input
            if (!qtyInput) return; // safety check

            const quantity = parseInt(qtyInput.value); // qty

            const priceText = item.querySelector('.item-price').textContent; // price text
            const price = parseFloat(priceText.replace(/[^0-9.]/g, '')); // extract number

            const itemTotal = price * quantity; // total for item
            subtotal += itemTotal; // add to subtotal

            const totalEl = item.querySelector('.item-total strong');
            if (totalEl) totalEl.textContent = '$' + itemTotal.toFixed(2); // update UI
        });

        const shipping = 15.00; // flat shipping
        const taxRate = 0.13; // tax %
        const tax = subtotal * taxRate; // tax amount
        const grandTotal = subtotal + shipping + tax; // final total

        const subtotalEl = document.getElementById('subtotal');
        if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toFixed(2); // update subtotal

        const taxEl = document.getElementById('tax');
        if (taxEl) taxEl.textContent = '$' + tax.toFixed(2); // update tax

        const grandTotalEl = document.getElementById('grand-total');
        if (grandTotalEl) grandTotalEl.innerHTML = '<strong>$' + grandTotal.toFixed(2) + '</strong>'; // update grand total

        const cartCount = document.querySelector('.cart-count'); // header count
        if (cartCount) {
            cartCount.textContent = cartItems.length + ' item(s)'; // update count
        }
    }
});
