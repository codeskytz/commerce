<?php
header('Content-Type: application/json');
require('../includes/db.php');
require('../includes/functions.php');

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'error' => 'No order ID provided']);
    exit();
}

try {
    $order_id = $_GET['order_id'];
    
    // Fetch items with product names
    // Join with products table to get current name (or use name stored in order_items if we had it, but schema says product_id)
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fallback if product was deleted
    foreach ($items as &$item) {
        if (!$item['name']) {
            $item['name'] = 'Unknown Product (ID: ' . $item['product_id'] . ')';
        }
        $item['formatted_price'] = format_price($item['price']);
        $item['formatted_subtotal'] = format_price($item['price'] * $item['quantity']);
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
