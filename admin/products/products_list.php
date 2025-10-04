<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');


?>

<h2> Manage Products </h2>

<?php

$query = 'SELECT * FROM product
        ORDER BY dateAdded';

$result = mysqli_query($connect, $query);

?>

<table border="1">
    <tr>
        
        <th>Title</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Size</th>
        <th>Photos</th>
        <th>Actions</th>

    </tr>

    <?php while($record = mysqli_fetch_assoc($result)): ?>

        <tr>
            <td> <?php echo $record['product_title']; ?></td>
            <td> <?php echo $record['product_desc']; ?></td>
            <td> <?php echo $record['product_price']; ?></td>
            <td> <?php echo $record['product_stock']; ?></td>
            <td> <?php echo $record['product_size']; ?></td>
            <td> No Photo yet</td>

            <td>
                <a href="products_edit.php?id=<?php echo $record['product_id']; ?>">Edit</a>
                <a href="delete_confirm.php?delete=<?php echo $record['product_id']; ?>">Delete</a>
            </td>
    </tr>

        <?php endwhile; ?>

    </table>
    <a href="products_add.php">Add Product</a>

