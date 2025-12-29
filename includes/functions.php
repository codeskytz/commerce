<?php
require_once __DIR__ . '/db.php';

function getSetting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

function updateSetting($key, $value) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Backward compatibility aliases
function get_setting($key) {
    return getSetting($key, null);
}

function update_setting($key, $value) {
    return updateSetting($key, $value);
}

function format_price($amount) {
    $symbol = getSetting('currency_symbol', '$');
    // Check if symbol requires space (e.g. TZS 500 vs $500)
    // For simplicity, if length > 1 assume space needed
    if (strlen($symbol) > 1) {
        return $symbol . ' ' . number_format($amount, 2);
    }
    return $symbol . number_format($amount, 2);
}
?>
