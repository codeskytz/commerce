<?php
include('includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<h2>My Orders</h2>

<?php if (count($orders) > 0): ?>
    <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <span class="order-id">#<?php echo $order['id']; ?></span>
                <?php
                $statusClass = 'status-pending';
                if ($order['status'] == 'completed') $statusClass = 'status-completed';
                if ($order['status'] == 'cancelled') $statusClass = 'status-cancelled';
                ?>
                <span class="order-status <?php echo $statusClass; ?>"><?php echo ucfirst($order['status']); ?></span>
            </div>
            
            <div class="order-details-row">
                <span style="color: var(--text-secondary);">Date</span>
                <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
            </div>
            <div class="order-details-row">
                <span style="color: var(--text-secondary);">Items</span>
                <span>
                    <?php 
                    // Fetch usage count or item names if complex, for now simple count or manual query if needed
                    // Doing a quick subquery count for display
                    $countStmt = $pdo->prepare("SELECT SUM(quantity) FROM order_items WHERE order_id = ?");
                    $countStmt->execute([$order['id']]);
                    echo $countStmt->fetchColumn() ?: 0;
                    ?> Items
                </span>
            </div>
             <div class="order-details-row" style="margin-top: 12px; align-items: center;">
                <span style="font-weight: 600;">Total Amount</span>
                <span class="order-total"><?php echo format_price($order['total_amount']); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div style="text-align: center; padding: 40px 20px;">
        <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #e2e8f0; margin-bottom: 16px;"></i>
        <h3>No orders yet</h3>
        <p style="color: var(--text-secondary); margin-bottom: 24px;">Start shopping to see your orders here.</p>
        <a href="index.php" class="btn">Start Shopping</a>
    </div>
<?php endif; ?>

<?php include('includes/footer.php'); ?>
