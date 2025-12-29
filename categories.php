<?php
include('includes/header.php');
?>
<h2>All Categories</h2>
<div class="category-list" style="margin-top: 16px;">
    <?php
    $allCats = $pdo->query("SELECT * FROM categories")->fetchAll();
    foreach($allCats as $c): 
    ?>
    <a href="index.php?category=<?php echo $c['id']; ?>" style="display: block; padding: 16px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: #fff;">
        <span style="font-weight: 500; font-size: 1rem;"><?php echo htmlspecialchars($c['name']); ?></span>
        <i class="fas fa-chevron-right" style="color: var(--text-secondary);"></i>
    </a>
    <?php endforeach; ?>
</div>
<?php include('includes/footer.php'); ?>
