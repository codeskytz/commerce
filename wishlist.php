<?php
include('includes/header.php');
?>

<div class="container">
    <h2 style="margin: 20px 0;">My Wishlist</h2>
    
    <div id="wishlist-display" class="grid-2-col">
        <!-- Wishlist items will be rendered here by JavaScript -->
    </div>
    
    <div id="empty-wishlist" style="text-align: center; padding: 60px 20px; display: none;">
        <i class="fas fa-heart" style="font-size: 4rem; color: #ddd; margin-bottom: 16px;"></i>
        <h3 style="color: var(--text-secondary);">Your wishlist is empty</h3>
        <p style="color: var(--text-secondary); margin-bottom: 24px;">Start adding items you love!</p>
        <a href="index.php" class="btn">Browse Products</a>
    </div>
</div>

<?php include('includes/footer.php'); ?>
