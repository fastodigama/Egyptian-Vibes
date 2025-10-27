</main>
</div> <!-- flex container row div closure -->

<footer id="footer">
    <small>&copy; <?php echo date("Y"); ?> Egyptian Vibes. All rights reserved.</small>
</footer>
<?php
// Get the current page filename (e.g., 'register.php', 'product_details.php')
$page = basename($_SERVER['PHP_SELF']);
?>

<!-- General script: used on all pages for shared functionality like image sliders or sidebar toggles -->
<script src="scripts/general-script.js" defer></script>

<?php if ($page === 'product_details.php'): ?>
    <!-- Product details script: only needed on the product details page -->
    <script src="scripts/product_details.js" defer></script>
<?php endif; ?>

<?php if ($page === 'view_cart.php'): ?>
    <!-- View cart script: only needed on the cart page -->
    <script src="scripts/view_cart.js" defer></script>
<?php endif; ?>

<?php if ($page === 'register.php'): ?>
    <!-- Registration form validation script: only needed on the registration page -->
    <script src="scripts/registerValidation.js" defer></script>
<?php endif; ?>

</body>

</html>