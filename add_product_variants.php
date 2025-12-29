<?php
require_once 'includes/db.php';

try {
    // Add colors and sizes columns to products table
    $pdo->exec("ALTER TABLE products ADD COLUMN colors TEXT NULL AFTER description");
    echo "✓ Added 'colors' column to products table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ Column 'colors' already exists.\n";
    } else {
        echo "✗ Error adding colors: " . $e->getMessage() . "\n";
    }
}

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN sizes TEXT NULL AFTER colors");
    echo "✓ Added 'sizes' column to products table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ Column 'sizes' already exists.\n";
    } else {
        echo "✗ Error adding sizes: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Database migration completed!\n";
?>
