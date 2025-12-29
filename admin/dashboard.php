<?php
require('../includes/db.php');
include('../includes/functions.php');
include('../includes/admin_header.php');

// Fetch counts
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Recent orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<h2>Dashboard Overview</h2>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Users</h3>
        <p class="value"><?php echo $userCount; ?></p>
    </div>
    <div class="dashboard-card">
        <h3>Products</h3>
        <p class="value"><?php echo $productCount; ?></p>
    </div>
    <div class="dashboard-card">
        <h3>Total Orders</h3>
        <p class="value"><?php echo $orderCount; ?></p>
    </div>
</div>

<h3>Recent Orders</h3>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Order Id</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo format_price($order['total_amount']); ?></td>
                <td><span style="padding: 4px 8px; border-radius: 4px; background: #e2e8f0; font-size: 0.8rem;"><?php echo ucfirst($order['status']); ?></span></td>
                <td><?php echo $order['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('../includes/admin_footer.php'); ?>
