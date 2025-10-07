<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Admin Users</h2>
        <a href="users_add.php" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Add User
        </a>
    </div>

    <?php
    $query = 'SELECT * FROM users ORDER BY last, first';
    $result = mysqli_query($connect, $query);
    ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Email Address</th>
                    <th scope="col">Active?</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['first']); ?></td>
                        <td><?php echo htmlspecialchars($record['last']); ?></td>
                        <td><?php echo htmlspecialchars($record['email']); ?></td>
                        <td>
                            <?php if ($record['active']): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="users_edit.php?id=<?php echo (int)$record['id']; ?>" 
                               class="btn btn-primary btn-sm me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="user_delete_confirm.php?delete=<?php echo (int)$record['id']; ?>" 
                               class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php'); ?>
