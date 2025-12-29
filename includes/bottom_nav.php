<nav class="bottom-nav">
    <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="categories.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
        <i class="fas fa-th-large"></i>
        <span>Categories</span>
    </a>
    <a href="wishlist.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'wishlist.php' ? 'active' : ''; ?>">
        <i class="fas fa-heart"></i>
        <span>Wishlist</span>
    </a>
    <a href="profile.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
</nav>
