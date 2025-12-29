<?php
require('../includes/db.php');
include('../includes/admin_header.php');

if (isset($_GET['delete'])) {
    // Prevent self-deletion
    if ($_GET['delete'] == $_SESSION['user_id']) {
        echo "<script>alert('Cannot delete yourself!');</script>";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: users.php");
        exit();
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<h2>Manage Users</h2>

<div class="dashboard-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span style="padding: 4px 8px; background: <?php echo $user['role'] === 'admin' ? '#dbeafe' : '#dcfce7'; ?>; color: <?php echo $user['role'] === 'admin' ? '#1e40af' : '#166534'; ?>; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                            <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php else: ?>
                            <span style="color: #ccc; font-size: 0.9rem;">(You)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
