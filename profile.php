<?php
include('includes/header.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<h2>My Profile</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>

<a href="my_orders.php" class="btn btn-block" style="margin-top: 24px; background: #fff; color: var(--text-primary); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
    <span><i class="fas fa-box" style="margin-right: 10px; color: var(--accent-color);"></i> My Orders</span>
    <i class="fas fa-chevron-right" style="font-size: 0.8rem; color: #ccc;"></i>
</a>

<a href="logout.php" class="btn" style="background: var(--danger-color); margin-top: 16px;">Logout</a>
<?php include('includes/footer.php'); ?>
