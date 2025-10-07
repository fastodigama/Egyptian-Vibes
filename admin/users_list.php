<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

?>

<h2>Admin Users</h2>

<?php

$query = 'SELECT * FROM users
          ORDER BY last, first';

$result = mysqli_query($connect, $query);

?>

<table border="1">
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email Address</th>
        <th>Active?</th>
        <th>Actions</th>
    </tr>

    <?php while ($record = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($record['first']); ?></td>
            <td><?php echo htmlspecialchars($record['last']); ?></td>
            <td><?php echo htmlspecialchars($record['email']); ?></td>
            <td><?php echo htmlspecialchars($record['active']); ?></td>
            <td>
                <a href="users_edit.php?id=<?php echo (int)$record['id']; ?>">Edit</a>
                <a href="user_delete_confirm.php?delete=<?php echo (int)$record['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="users_add.php">Add user</a>
