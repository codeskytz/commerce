<?php
require_once 'includes/db.php';

try {
    // Add icon column to categories table
    $pdo->exec("ALTER TABLE categories ADD COLUMN icon VARCHAR(100) DEFAULT 'fa-tag' AFTER name");
    echo "✓ Successfully added 'icon' column to categories table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ Column 'icon' already exists.\n";
    } else {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}
?>
