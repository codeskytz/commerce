<?php
require_once '../includes/db.php';
include('../includes/admin_header.php');

$message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM carousel_slides WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Slide deleted successfully.";
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $btn_text = $_POST['button_text'];
    $btn_link = $_POST['button_link'];
    $bg_color = $_POST['background_color'];
    $grad_end = $_POST['gradient_end'];
    $order = $_POST['sort_order'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE carousel_slides SET title=?, subtitle=?, button_text=?, button_link=?, background_color=?, gradient_end=?, sort_order=? WHERE id=?");
        $stmt->execute([$title, $subtitle, $btn_text, $btn_link, $bg_color, $grad_end, $order, $_POST['id']]);
        $message = "Slide updated successfully.";
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO carousel_slides (title, subtitle, button_text, button_link, background_color, gradient_end, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $subtitle, $btn_text, $btn_link, $bg_color, $grad_end, $order]);
        $message = "Slide added successfully.";
    }
}

$slides = $pdo->query("SELECT * FROM carousel_slides ORDER BY sort_order ASC")->fetchAll();
$editingSlide = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM carousel_slides WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editingSlide = $stmt->fetch();
}
?>

<h2>Manage Carousel Slides</h2>

<?php if ($message): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="admin-carousel-container">
    <!-- Form -->
    <!-- Form -->
    <div class="dashboard-card admin-carousel-form">
        <h3><?php echo $editingSlide ? 'Edit Slide' : 'Add New Slide'; ?></h3>
        
        <style>
            .carousel-form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
            .carousel-form-full {
                grid-column: 1 / -1;
            }
            .form-group {
                margin-bottom: 0; /* Reset for grid */
            }
            .color-input-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
                border: 1px solid #ddd;
                padding: 5px;
                border-radius: 4px;
            }
            .color-input-wrapper input[type="color"] {
                border: none;
                width: 40px;
                height: 40px;
                padding: 0;
                cursor: pointer;
                background: none;
            }
        </style>

        <form action="carousel.php" method="POST" class="carousel-form-grid">
            <?php if ($editingSlide): ?>
                <input type="hidden" name="id" value="<?php echo $editingSlide['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group carousel-form-full">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($editingSlide['title'] ?? ''); ?>" required placeholder="Main Headline">
            </div>
            
            <div class="form-group carousel-form-full">
                <label>Subtitle</label>
                <input type="text" name="subtitle" value="<?php echo htmlspecialchars($editingSlide['subtitle'] ?? ''); ?>" required placeholder="Subtext or Description">
            </div>
            
            <div class="form-group">
                <label>Button Text</label>
                <input type="text" name="button_text" value="<?php echo htmlspecialchars($editingSlide['button_text'] ?? ''); ?>" required placeholder="e.g. Shop Now">
            </div>
            
            <div class="form-group">
                <label>Button Link</label>
                <input type="text" name="button_link" value="<?php echo htmlspecialchars($editingSlide['button_link'] ?? '#'); ?>" placeholder="e.g. index.php?category=1">
            </div>
            
            <div class="form-group">
                <label>Background Color (Start)</label>
                <div class="color-input-wrapper">
                    <input type="color" name="background_color" value="<?php echo htmlspecialchars($editingSlide['background_color'] ?? '#0f172a'); ?>">
                    <span style="font-size: 0.85rem; color: #666;">Choose Start Color</span>
                </div>
            </div>
            
            <div class="form-group">
                <label>Background Color (End)</label>
                <div class="color-input-wrapper">
                    <input type="color" name="gradient_end" value="<?php echo htmlspecialchars($editingSlide['gradient_end'] ?? '#3b82f6'); ?>">
                    <span style="font-size: 0.85rem; color: #666;">Choose End Color</span>
                </div>
            </div>
            
            <div class="form-group carousel-form-full">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo htmlspecialchars($editingSlide['sort_order'] ?? '0'); ?>" placeholder="0">
                <small style="color: #666;">Lower numbers appear first</small>
            </div>
            
            <div class="carousel-form-full" style="margin-top: 10px;">
                <button type="submit" class="btn"><?php echo $editingSlide ? 'Update Slide' : 'Add Slide'; ?></button>
                <?php if ($editingSlide): ?>
                    <a href="carousel.php" class="btn" style="background: #6c757d; margin-left: 10px;">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="dashboard-card admin-carousel-list">
        <h3>Existing Slides</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Colors</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slides as $s): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($s['title']); ?></strong><br>
                            <small><?php echo htmlspecialchars($s['subtitle']); ?></small>
                        </td>
                        <td>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, <?php echo $s['background_color']; ?>, <?php echo $s['gradient_end']; ?>);"></div>
                        </td>
                        <td><?php echo $s['sort_order']; ?></td>
                        <td>
                            <a href="carousel.php?edit=<?php echo $s['id']; ?>" style="color: var(--accent-color); margin-right: 10px;">Edit</a>
                            <a href="carousel.php?delete=<?php echo $s['id']; ?>" style="color: var(--danger-color);" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
