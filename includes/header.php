<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get categories and settings
require_once __DIR__ . '/functions.php';
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
$site_title = get_setting('site_title') ?: 'JJ.MOBISHOP';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css?v=<?php echo file_exists('assets/style.css') ? filemtime('assets/style.css') : time(); ?>">
    <script>
        const CURRENCY_SYMBOL = "<?php echo htmlspecialchars(get_setting('currency_symbol') ?: '$'); ?>";
    </script>
</head>
<body>
    
    <!-- Top Sticky Header -->
    <header class="main-header">
        <div class="header-left" style="display: flex; align-items: center;">
            <button class="icon-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <a href="index.php" class="site-brand"><?php echo htmlspecialchars($site_title); ?></a>
        </div>
        <!-- Search -->
        <div class="header-center">
            <form action="index.php" method="GET" class="search-form">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon-inside"></i>
                    <input type="text" 
                           name="search" 
                           id="search-input"
                           class="search-input" 
                           placeholder="Search products..." 
                           autocomplete="off"
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <div id="search-suggestions" class="search-suggestions"></div>
                </div>
            </form>
        </div>
        
        <div class="header-right">
            <a href="checkout.php" class="icon-btn cart-badge">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge-count" id="cart-count">0</span>
            </a>
        </div>
    </header>

    <!-- Mobile Sidebar -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <div class="mobile-sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
            <button class="icon-btn" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
        </div>
        <a href="index.php" class="sidebar-link"><i class="fas fa-home"></i> Home</a>
        <a href="categories.php" class="sidebar-link"><i class="fas fa-th-large"></i> Categories</a>
        <a href="wishlist.php" class="sidebar-link"><i class="fas fa-heart"></i> Wishlist</a>
        <a href="profile.php" class="sidebar-link"><i class="fas fa-user"></i> Profile</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="my_orders.php" class="sidebar-link"><i class="fas fa-box"></i> My Orders</a>
            <a href="logout.php" class="sidebar-link" style="color: var(--danger-color);"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php" class="sidebar-link"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </div>

    <div class="container main-content">
