<?php
require('../includes/db.php');
include('../includes/functions.php');
include('../includes/admin_header.php');

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Create/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $shipping_fee = $_POST['shipping_fee'] !== '' ? $_POST['shipping_fee'] : 0;
    $description = $_POST['description'];
    $colors = $_POST['colors'] ?? ''; // Optional
    $sizes = $_POST['sizes'] ?? ''; // Optional
    $id = $_POST['id'] ?? null;
    $imagePath = $_POST['current_image'] ?? '';

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        
        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $fileName;
        
        // Check for upload errors
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $error = "Upload failed with error code: " . $_FILES['image']['error'];
        } else {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = mime_content_type($_FILES['image']['tmp_name']);
            
            if (!in_array($file_type, $allowed_types)) {
                $error = "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.";
            } else {
                // Attempt to move uploaded file
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $imagePath = "uploads/" . $fileName; // Store relative path for frontend
                } else {
                    $error = "Failed to move uploaded file. Check directory permissions.";
                }
            }
        }
    }

    if (!$error) {
        try {
            if ($id) {
                // Update
                $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, price=?, shipping_fee=?, description=?, colors=?, sizes=?, image=? WHERE id=?");
                $stmt->execute([$name, $category_id, $price, $shipping_fee, $description, $colors, $sizes, $imagePath, $id]);
                $success = "Product updated!";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, shipping_fee, description, colors, sizes, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category_id, $price, $shipping_fee, $description, $colors, $sizes, $imagePath]);
                $success = "Product added!";
            }
            // Refresh to list
            echo "<script>window.location.href='products.php';</script>"; 
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: products.php");
    exit();
}

// Fetch Categories for Dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($action === 'add' || $action === 'edit') {
    $product = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $product = $stmt->fetch();
    }
?>
    <h2><?php echo $action === 'edit' ? 'Edit Product' : 'Add Product'; ?></h2>
    
    <?php if ($error): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" style="background:#fff; padding:20px; border-radius:8px; max-width:600px;">
        <input type="hidden" name="id" value="<?php echo $product['id'] ?? ''; ?>">
        <input type="hidden" name="current_image" value="<?php echo $product['image'] ?? ''; ?>">
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $product['name'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo $cat['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group" style="flex:1;">
                <label>Price</label>
                <input type="number" step="0.01" name="price" value="<?php echo $product['price'] ?? ''; ?>" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label>Shipping Fee (Optional)</label>
                <input type="number" step="0.01" name="shipping_fee" value="<?php echo $product['shipping_fee'] ?? '0.00'; ?>" placeholder="0.00">
                <small style="color: var(--text-secondary); font-size: 0.8rem;">Leave empty for 0.</small>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4"><?php echo $product['description'] ?? ''; ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group" style="flex:1;">
                <label>Colors (Optional)</label>
                <input type="text" name="colors" value="<?php echo htmlspecialchars($product['colors'] ?? ''); ?>" placeholder="e.g. Red, Blue, Black">
                <small style="color: var(--text-secondary); font-size: 0.8rem;">Comma-separated values. Leave empty if not applicable.</small>
            </div>
            <div class="form-group" style="flex:1;">
                <label>Sizes (Optional)</label>
                <input type="text" name="sizes" value="<?php echo htmlspecialchars($product['sizes'] ?? ''); ?>" placeholder="e.g. S, M, L, XL">
                <small style="color: var(--text-secondary); font-size: 0.8rem;">Comma-separated values. Leave empty if not applicable.</small>
            </div>
        </div>
        
        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
            <?php if(!empty($product['image'])): ?>
                <p>Current: <img src="../<?php echo $product['image']; ?>" width="50"></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn">Save</button>
        <a href="products.php" class="btn btn-danger">Cancel</a>
    </form>
<?php
} else {
    // List Mode
    $products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
?>
    <h2>Products <a href="products.php?action=add" class="btn" style="float:right; font-size:0.8rem;">Add New</a></h2>
    <table>
        <thead>
            <tr>
                <th>Img</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $p): ?>
            <tr>
                <td><?php if($p['image']) echo "<img src='../{$p['image']}' width='50'>"; ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['category_name']); ?></td>
                <td><?php echo format_price($p['price']); ?></td>
                <td>
                    <a href="products.php?action=edit&id=<?php echo $p['id']; ?>" class="btn">Edit</a>
                    <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php
}
include('../includes/admin_footer.php');
?>
