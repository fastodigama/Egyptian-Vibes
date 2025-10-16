<?php
include('frontend_includes/config.php');
// Handle item removal
if(isset($_GET['remove'])){
    $removeId = $_GET['remove'];

    unset($_SESSION['cart'][$removeId]);
    header('Location: view_cart.php');
    die;
}

//handle update quantity

if($_SERVER['REQUEST_METHOD']=== 'POST' && isset($_POST['update_qty'])) {
    $updateId = $_POST['product_id']; //product id from the hidden input in the qty update form
    $newQty = (int) $_POST['new_quantity'];

    if(isset($_SESSION['cart'][$updateId])){
        if($newQty >0){
            $_SESSION['cart'][$updateId]['quantity'] = $newQty; //nested session array
        }else {
            unset($_SESSION['cart'][$updateId]); //remove if qty is 0
        }
    }
}



include('frontend_includes/header.php');

?>

<h1>Your Cart</h1>

<?php if (!empty($_SESSION['cart'])): ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grandTotal = 0;
            foreach ($_SESSION['cart'] as $id => $item): // Loop through each product (id and details) saved in the user's cart session
                $total = $item['price'] * $item['quantity'];
                $grandTotal += $total;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
               
                <td>
                    <form action="view_cart.php" method="post" style="display:flex; gap:4px;">
                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                        <input type="number" name="new_quantity" id="" value="<?php echo $item['quantity']; ?>">
                        <button type="submit" name="update_qty">Update</button>
                        </td>
                    </form>
                    
                
                <td>$<?php echo number_format($total, 2); ?></td>
                 <td>
                <a href="view_cart.php?remove=<?php echo $id; ?>" class="remove-btn">Remove</a>
            </td>
            

            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Grand Total</strong></td>
                <td><strong>$<?php echo number_format($grandTotal, 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>

<?php include('frontend_includes/footer.php'); ?>
