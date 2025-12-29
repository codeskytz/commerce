<?php
require_once 'includes/db.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS `carousel_slides` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `title` VARCHAR(255),
      `subtitle` VARCHAR(255),
      `button_text` VARCHAR(50),
      `button_link` VARCHAR(255),
      `background_color` VARCHAR(50) DEFAULT '#0f172a', /* Default primary color */
      `gradient_end` VARCHAR(50) DEFAULT '#3b82f6', /* Default accent color */
      `sort_order` INT DEFAULT 0,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    
    -- Insert default slides if empty
    INSERT INTO `carousel_slides` (`title`, `subtitle`, `button_text`, `button_link`, `background_color`, `gradient_end`, `sort_order`)
    SELECT 'Summer Sale', 'Up to 50% Off', 'Shop Now', '#', '#0f172a', '#3b82f6', 1
    WHERE NOT EXISTS (SELECT 1 FROM carousel_slides);
    
    INSERT INTO `carousel_slides` (`title`, `subtitle`, `button_text`, `button_link`, `background_color`, `gradient_end`, `sort_order`)
    SELECT 'New Arrivals', 'Check out the latest trends', 'View More', '#', '#10b981', '#059669', 2
    WHERE NOT EXISTS (SELECT 1 FROM carousel_slides LIMIT 1 OFFSET 1);
    ";
    
    $pdo->exec($sql);
    echo "Carousel table created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
