<?php
require('../includes/db.php');
include('../includes/admin_header.php');

// Popular category icons for e-commerce
$available_icons = [
    'fa-shirt' => 'Clothing/Apparel',
    'fa-mobile-screen-button' => 'Mobile/Electronics',
    'fa-laptop' => 'Laptops/Computers',
    'fa-headphones' => 'Audio/Headphones',
    'fa-camera' => 'Camera/Photography',
    'fa-tv' => 'TV/Monitors',
    'fa-gamepad' => 'Gaming',
    'fa-book' => 'Books',
    'fa-utensils' => 'Food/Kitchen',
    'fa-couch' => 'Furniture',
    'fa-shoe-prints' => 'Shoes/Footwear',
    'fa-gem' => 'Jewelry/Accessories',
    'fa-watch' => 'Watches',
    'fa-bag-shopping' => 'Shopping/Bags',
    'fa-baby' => 'Baby/Kids',
    'fa-dumbbell' => 'Sports/Fitness',
    'fa-car' => 'Automotive',
    'fa-paw' => 'Pets',
    'fa-paintbrush' => 'Art/Crafts',
    'fa-toolbox' => 'Tools/Hardware',
    'fa-home' => 'Home/Garden',
    'fa-briefcase' => 'Office/Business',
    'fa-music' => 'Music/Instruments',
    'fa-graduation-cap' => 'Education',
    'fa-heartbeat' => 'Health/Beauty',
    'fa-bicycle' => 'Bikes/Cycling',
    'fa-gift' => 'Gifts',
    'fa-tag' => 'General/Other'
];

// Handle Add/Edit Category
if (isset($_POST['save_category'])) {
    $name = trim($_POST['name']);
    $icon = $_POST['icon'] ?? 'fa-tag';
    $id = $_POST['id'] ?? null;
    
    if (!empty($name)) {
        if ($id) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE categories SET name=?, icon=? WHERE id=?");
            $stmt->execute([$name, $icon, $id]);
        } else {
            // Insert new
            $stmt = $pdo->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
            $stmt->execute([$name, $icon]);
        }
        header("Location: categories.php");
        exit();
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: categories.php");
    exit();
}

// Check if editing
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<h2>Manage Categories</h2>

<div class="dashboard-card" style="margin-bottom: 20px; max-width: 700px;">
    <h3><?php echo $editing ? 'Edit Category' : 'Add New Category'; ?></h3>
    <form method="POST" action="">
        <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Category Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($editing['name'] ?? ''); ?>" placeholder="e.g., Electronics" required>
        </div>
        
        <div class="form-group">
            <label>Icon</label>
            <select name="icon" id="icon-select" required style="padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                <?php foreach ($available_icons as $iconClass => $iconLabel): ?>
                    <option value="<?php echo $iconClass; ?>" 
                            <?php echo ($editing && $editing['icon'] === $iconClass) ? 'selected' : ''; ?>>
                        <?php echo $iconLabel; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div style="margin-top: 12px; padding: 16px; background: #f8fafc; border-radius: 8px; text-align: center;">
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 8px;">Preview:</p>
                <i id="icon-preview" class="fas <?php echo $editing['icon'] ?? 'fa-tag'; ?>" style="font-size: 3rem; color: var(--primary-color);"></i>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" name="save_category" class="btn"><?php echo $editing ? 'Update' : 'Add'; ?> Category</button>
            <?php if ($editing): ?>
                <a href="categories.php" class="btn btn-danger">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="dashboard-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><i class="fas <?php echo $cat['icon'] ?? 'fa-tag'; ?>" style="font-size: 1.5rem; color: var(--accent-color);"></i></td>
                    <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                    <td><?php echo date('M d, Y', strtotime($cat['created_at'])); ?></td>
                    <td>
                        <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="btn" style="font-size: 0.85rem; padding: 6px 12px;">Edit</a>
                        <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn btn-danger" style="font-size: 0.85rem; padding: 6px 12px;" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Live icon preview
document.getElementById('icon-select').addEventListener('change', function() {
    const preview = document.getElementById('icon-preview');
    preview.className = 'fas ' + this.value;
});
</script>

<?php include('../includes/admin_footer.php'); ?>
