<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$site_title = get_setting('site_title') ?: 'JJ.MOBISHOP';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo htmlspecialchars($site_title); ?></title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css?v=<?php echo file_exists('../assets/style.css') ? filemtime('../assets/style.css') : time(); ?>">
    <script src="../assets/script.js?v=<?php echo file_exists('../assets/script.js') ? filemtime('../assets/script.js') : time(); ?>" defer></script>
</head>
<body>
    <div class="admin-layout">
        <!-- Mobile Header (Visible only on mobile) -->
        <div class="admin-mobile-header">
            <button class="admin-toggle-btn" onclick="toggleAdminSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <span class="admin-brand"><?php echo htmlspecialchars($site_title); ?> Admin</span>
        </div>

        <div class="sidebar" id="admin-sidebar">
            <div class="sidebar-brand-desktop">
                <h3><?php echo htmlspecialchars($site_title); ?> Admin</h3>
            </div>
            <nav>
                <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>"><i class="fas fa-th-list"></i> Categories</a>
                <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Users</a>
                <a href="carousel.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'carousel.php' ? 'active' : ''; ?>"><i class="fas fa-images"></i> Carousel</a>
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a>
                <a href="../logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        
        <!-- Overlay for mobile sidebar -->
        <div class="admin-sidebar-overlay" onclick="toggleAdminSidebar()"></div>

        <div class="main-content">
            <header class="admin-desktop-header">
                <style>
                    .admin-desktop-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        background: white;
                        padding: 15px 30px;
                        margin-bottom: 30px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                        border-radius: 8px;
                    }
                    .admin-header-title h2 {
                        margin: 0;
                        font-size: 1.5rem;
                        color: #1e293b;
                    }
                    .admin-header-actions {
                        display: flex;
                        gap: 15px;
                        align-items: center;
                    }
                    .admin-btn-outline {
                        border: 1px solid #e2e8f0;
                        background: white;
                        color: #64748b;
                        padding: 8px 16px;
                        border-radius: 6px;
                        text-decoration: none;
                        font-size: 0.9rem;
                        transition: all 0.2s;
                    }
                    .admin-btn-outline:hover {
                        border-color: #cbd5e1;
                        color: #1e293b;
                    }
                    .admin-profile {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    }
                    .admin-avatar {
                        width: 36px;
                        height: 36px;
                        background: #dbeafe;
                        color: #2563eb;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: 600;
                    }
                    @media (max-width: 768px) {
                        .admin-desktop-header {
                            display: none; /* Mobile has its own header */
                        }
                    }
                </style>
                <div class="admin-header-title">
                    <h2>Admin Panel</h2>
                </div>
                <div class="admin-header-actions">
                    <a href="../index.php" target="_blank" class="admin-btn-outline">
                        <i class="fas fa-external-link-alt"></i> Visit Shop
                    </a>
                    <div class="admin-profile">
                        <div class="admin-avatar">A</div>
                        <span style="font-weight: 500; font-size: 0.95rem;">Administrator</span>
                    </div>
                </div>
            </header>
