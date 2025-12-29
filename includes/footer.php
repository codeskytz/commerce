    </div> <!-- Close container -->
    
    <footer style="margin-bottom: var(--bottom-nav-height); text-align: center; padding: 20px; color: var(--text-secondary); font-size: 0.8rem;">
        <p>&copy; <?php echo date('Y'); ?> <?php global $site_title; echo htmlspecialchars($site_title ?? 'JJ.MOBISHOP'); ?>. All rights reserved.</p>
    </footer>

    <?php include(__DIR__ . '/bottom_nav.php'); ?>
    <script src="assets/script.js?v=<?php echo file_exists('assets/script.js') ? filemtime('assets/script.js') : time(); ?>"></script>
</body>
</html>
