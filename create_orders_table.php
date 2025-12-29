<?php
require_once 'includes/db.php';

try {
    // Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            customer_phone VARCHAR(50) NOT NULL,
            customer_address TEXT NOT NULL,
            notes TEXT NULL,
            total_amount DECIMAL(10, 2) NOT NULL,
            order_items TEXT NOT NULL,
            status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    echo "✓ Orders table created successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
