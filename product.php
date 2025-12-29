<?php
require('includes/db.php');
include('includes/header.php');

$id = $_GET['id'] ?? null;
if (!$id) die("Product not found");

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) die("Product not found");

// Parse colors and sizes
$colors = !empty($product['colors']) ? array_map('trim', explode(',', $product['colors'])) : [];
$sizes = !empty($product['sizes']) ? array_map('trim', explode(',', $product['sizes'])) : [];
?>

<div class="pdp-layout">
    <div class="pdp-image-container">
        <?php if($product['image']): ?>
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
            <div style="font-size: 2rem; color: #ccc;">No Image</div>
        <?php endif; ?>
    </div>

    <div class="pdp-details">
        <h1 class="pdp-title"><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="pdp-price"><?php echo format_price($product['price']); ?></div>
        
        <div style="display: flex; align-items: center; margin-bottom: 24px;">
            <div style="color: #f59e0b; margin-right: 8px;">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
            </div>
            <span style="color: var(--text-secondary); font-size: 0.9rem;">(4.5/5)</span>
        </div>

        <?php if (!empty($colors)): ?>
        <div style="margin-bottom: 24px;">
            <div class="pdp-section-title">Color</div>
            <div class="variant-options">
                <?php foreach ($colors as $index => $color): ?>
                    <div class="variant-pill <?php echo $index === 0 ? 'selected' : ''; ?>" data-variant-type="color" onclick="selectVariant(this)">
                        <?php echo htmlspecialchars($color); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($sizes)): ?>
        <div style="margin-bottom: 24px;">
            <div class="pdp-section-title">Size</div>
            <div class="variant-options">
                <?php foreach ($sizes as $index => $size): ?>
                    <div class="variant-pill <?php echo $index === 0 ? 'selected' : ''; ?>" data-variant-type="size" onclick="selectVariant(this)">
                        <?php echo htmlspecialchars($size); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($product['description']): ?>
        <div class="pdp-description">
            <h3 class="pdp-section-title">Description</h3>
            <p style="color: var(--text-secondary); line-height: 1.6;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Sticky Action Bar -->
<div class="sticky-action-bar">
    <button class="wishlist-btn" 
            data-wishlist-id="<?php echo $product['id']; ?>"
            onclick="addToWishlist(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>', <?php echo $product['shipping_fee'] ?: 0; ?>)"
            title="Add to wishlist"
            style="flex: 0;">
        <i class="far fa-heart"></i>
    </button>
    <button class="btn" style="flex: 1; font-size: 1.1rem; justify-content: space-between; padding: 0 24px;" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes(htmlspecialchars($product['name'])); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>', <?php echo $product['shipping_fee'] ?: 0; ?>)">
        <span>Add to Cart</span>
        <span><?php echo format_price($product['price']); ?></span>
    </button>
</div>

<script>
function selectVariant(element) {
    // Remove selected class from siblings
    const siblings = element.parentElement.querySelectorAll('.variant-pill');
    siblings.forEach(pill => pill.classList.remove('selected'));
    
    // Add selected class to clicked element
    element.classList.add('selected');
}
</script>

<?php include('includes/footer.php'); ?>
