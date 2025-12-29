<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
     echo json_encode(['success' => false, 'error' => 'Invalid data']);
     exit();
}

try {
    $pdo->beginTransaction();

    $user_id = $_SESSION['user_id'];
    // Combine phone and address since schema only has address
    $full_address = "Phone: " . $data['customer_phone'] . "\nAddress: " . $data['customer_address'];
    if (!empty($data['notes'])) {
        $full_address .= "\nNotes: " . $data['notes'];
    }
    
    // Insert order into database
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, address, status, created_at)
        VALUES (?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->execute([
        $user_id,
        $data['total_amount'],
        $full_address
    ]);
    
    $order_id = $pdo->lastInsertId();
    
    // Insert order items
    if (isset($data['items']) && !empty($data['items'])) {
        $items = json_decode($data['items'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
             // Maybe it was already an array? checkout.php sends JSON.stringify(cart) which is a string.
             // But json_decode(php://input) might have already decoded that string if it was nested? 
             // checkout.php: items: JSON.stringify(cart)
             // So $data['items'] is a STRING representing the JSON array.
             // So we need to decode it.
        } else {
             // if it was already an array (unlikely given the checkout code)
        }
        
        // Safety check if items is NOT an array after decode
        if (!is_array($items)) {
             // Try assuming it was passed as array directly?
             $items = $data['items'];
        }
    } else {
        $items = [];
    }

    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($items as $item) {
        // Ensure we have product_id, quantity, price
        // checkout.php cart items have: id, name, price, quantity, image
        $itemStmt->execute([
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order saved successfully'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
