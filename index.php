<?php
require('includes/db.php');
include('includes/header.php');

$categoryId = $_GET['category'] ?? null;
$searchQuery = $_GET['search'] ?? null;

if ($categoryId) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$categoryId]);
    $products = $stmt->fetchAll();
    
    // Get category name for title
    $catStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $catStmt->execute([$categoryId]);
    $catName = $catStmt->fetchColumn();
    $title = $catName . " Products";
} elseif ($searchQuery) {
    // Handle Search Query with typo tolerance
    $searchQuery = trim($searchQuery); // Trim the search query
    if (!empty($searchQuery)) {
        $searchTerm = "%{$searchQuery}%";
        
        // Multi-strategy search
        // Initially search by name using LIKE
        $sql = "SELECT DISTINCT p.* FROM products p WHERE p.name LIKE ?";
        $params = [$searchTerm];
        
        // Add SOUNDEX matching for better typo tolerance if query is long enough
        if (strlen($searchQuery) >= 3) {
            $sql .= " OR SOUNDEX(p.name) = SOUNDEX(?)";
            $params[] = $searchQuery; // Add the raw search query for SOUNDEX
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $products = $stmt->fetchAll();
    } else {
        $products = []; // No products if search query is empty after trimming
    }
    $title = "Search Results: " . htmlspecialchars($searchQuery);
} else {
    $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
    $title = "All Products";
}
?>

<?php if (!$categoryId && !$searchQuery): 
    // Fetch Slides
    $slides = $pdo->query("SELECT * FROM carousel_slides ORDER BY sort_order ASC")->fetchAll();
    ?>
    <!-- Hero Section -->
    <div class="hero-carousel">
        <?php foreach($slides as $s): ?>
        <div class="hero-slide" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($s['background_color']); ?> 0%, <?php echo htmlspecialchars($s['gradient_end'] ?? $s['background_color']); ?> 100%);">
            <div class="hero-content">
                <h2><?php echo htmlspecialchars($s['title']); ?></h2>
                <p><?php echo htmlspecialchars($s['subtitle']); ?></p>
                <a href="<?php echo htmlspecialchars($s['button_link']); ?>" class="btn" style="background: white; color: <?php echo htmlspecialchars($s['background_color']); ?>;"><?php echo htmlspecialchars($s['button_text']); ?></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Categories -->
    <h3 style="margin: 16px 0 8px;">Categories</h3>
    <div class="quick-categories">
        <?php 
        $cats = $pdo->query("SELECT * FROM categories LIMIT 10")->fetchAll();
        foreach($cats as $cat): 
        ?>
        <a href="index.php?category=<?php echo $cat['id']; ?>" class="cat-pill">
            <div class="cat-circle" style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--accent-color) 0%, #60a5fa 100%);">
                <i class="fas <?php echo $cat['icon'] ?? 'fa-tag'; ?>" style="font-size: 1.8rem; color: white;"></i>
            </div>
            <span class="cat-label"><?php echo htmlspecialchars($cat['name']); ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    
    <h3 style="margin: 24px 0 16px;">Trending Now</h3>
<?php else: ?>
    <!-- PLP Header -->
    <div style="margin: 16px 0;">
        <h2><?php echo htmlspecialchars($title); ?></h2>
        <p style="color: var(--text-secondary);"><?php echo count($products); ?> items</p>
        
        <!-- Filter/Sort Bar -->
        <div style="display: flex; gap: 16px; margin-top: 16px;">
            <button class="btn" style="flex:1; background: #fff; color: var(--text-primary); border: 1px solid var(--border-color);">Filter</button>
            <button class="btn" style="flex:1; background: #fff; color: var(--text-primary); border: 1px solid var(--border-color);">Sort</button>
        </div>
    </div>
<?php endif; ?>

<!-- Product Grid -->
<div class="grid-2-col">
    <?php if(count($products) > 0): ?>
        <?php foreach($products as $p): ?>
        <div class="product-card">
            <div class="product-image-container">
                <a href="product.php?id=<?php echo $p['id']; ?>">
                    <img src="<?php echo $p['image'] ?: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjUwIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjI1MCIgZmlsbD0iI2UyZThmMCIvPjwvc3ZnPg=='; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                </a>
                <button class="wishlist-btn" 
                        data-wishlist-id="<?php echo $p['id']; ?>"
                        onclick="addToWishlist(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>', <?php echo $p['price']; ?>, '<?php echo $p['image']; ?>', <?php echo $p['shipping_fee'] ?: 0; ?>)"
                        title="Add to wishlist">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <a href="product.php?id=<?php echo $p['id']; ?>" class="product-info" style="text-decoration: none; color: inherit;">
                <h3 class="product-title"><?php echo htmlspecialchars($p['name']); ?></h3>
                <div class="product-price"><?php echo format_price($p['price']); ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>
