<?php
require_once 'includes/db.php';

try {
    // Create settings table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            setting_key VARCHAR(100) PRIMARY KEY,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Settings table created/verified.\n";
    
    // Insert default settings if they don't exist
    $defaults = [
        'site_title' => 'JJ.MOBISHOP',
        'currency_symbol' => 'TZS',
        'whatsapp_number' => '+255123456789'
    ];
    
    foreach ($defaults as $key => $value) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $value]);
    }
    
    echo "✓ Default settings initialized.\n";
    echo "\n✅ Settings table setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
