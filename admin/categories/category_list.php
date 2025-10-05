<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');


?>

<h2> Manage Categories </h2>

<?php

$query = 'SELECT * FROM category
        ORDER BY category_name';

$result = mysqli_query($connect, $query);

?>

<table border="1">
    <tr>
        <th>Category</th>
        <th>Actions</th>
        

    </tr>

    <?php while($record = mysqli_fetch_assoc($result)): ?>

        <tr>
            <td> <?php echo htmlspecialchars($record['category_name']); ?></td>
            

            <td>
                <a href="category_edit.php?id=<?php echo $record['category_id']; ?>">Edit</a>
                <a href="delete_confirm.php?delete=<?php echo $record['category_id']; ?>">Delete</a>
            </td>
    </tr>

        <?php endwhile; ?>

    </table>
    <a href="category_add.php">Add category</a>

