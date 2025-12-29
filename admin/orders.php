<?php
require('../includes/db.php');
include('../includes/functions.php');
include('../includes/admin_header.php');

// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    header("Location: orders.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    
    // Also delete items (cascade should handle it but good to be safe if foreign keys aren't perfect)
    // database.sql says ON DELETE CASCADE so strictly not required.
    
    header("Location: orders.php");
    exit();
}

// Fetch orders with user details
$orders = $pdo->query("
    SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
")->fetchAll();
?>

<div class="header-flex">
    <h2>Manage Orders</h2>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Address/Phone</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td>
                    <div style="font-weight: 500;"><?php echo htmlspecialchars($order['user_name'] ?? 'Unknown'); ?></div>
                    <div style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($order['user_email'] ?? ''); ?></div>
                </td>
                <td style="max-width: 250px; font-size: 0.9rem; white-space: pre-wrap;"><?php echo htmlspecialchars($order['address']); ?></td>
                <td><?php echo number_format($order['total_amount'], 2); ?></td>
                <td>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="update_status" value="1">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status" onchange="this.form.submit()" class="status-select <?php echo $order['status']; ?>">
                            <option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Pending</option>
                            <option value="processing" <?php if($order['status']=='processing') echo 'selected'; ?>>Processing</option>
                            <option value="completed" <?php if($order['status']=='completed') echo 'selected'; ?>>Completed</option>
                            <option value="cancelled" <?php if($order['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </form>
                </td>
                <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-view" onclick="viewOrder(<?php echo $order['id']; ?>)">View Items</button>
                    <a href="orders.php?delete=<?php echo $order['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete order?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px;">
        <span class="close" onclick="closeModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h3 id="modalTitle">Order Details</h3>
        <div id="modalBody" style="margin-top: 20px;">
            Loading...
        </div>
    </div>
</div>

<script>
function viewOrder(orderId) {
    const modal = document.getElementById('orderModal');
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');
    
    modal.style.display = "block";
    modalTitle.innerText = "Order #" + orderId + " Details";
    modalBody.innerHTML = '<div style="text-align:center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading items...</div>';
    
    // Fetch order items
    fetch('get_order_items.php?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let html = '<table style="width:100%; border-collapse: collapse;">';
                html += '<tr style="border-bottom: 1px solid #eee; text-align: left;"><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>';
                
                let total = 0;
                data.items.forEach(item => {
                    const subtotal = item.price * item.quantity;
                    total += subtotal;
                    html += `<tr style="border-bottom: 1px solid #f9f9f9;">
                        <td style="padding: 10px 0;">${item.name}</td>
                        <td>${item.formatted_price}</td>
                        <td>${item.quantity}</td>
                        <td>${item.formatted_subtotal}</td>
                    </tr>`;
                });
                
                // For the final total line, we need the symbol.
                // We'll rely on our injected CURRENCY_SYMBOL constant or regex it from formatted string.
                // Let's assume the currency symbol is passed in the page.
                const symbol = '<?php echo getSetting('currency_symbol', '$'); ?>';
                
                html += `<tr style="border-top: 2px solid #eee; font-weight: bold;">
                    <td colspan="3" style="padding-top: 10px; text-align: right;">Total:</td>
                    <td style="padding-top: 10px;">${symbol} ${total.toFixed(2)}</td>
                </tr>`;
                html += '</table>';
                
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<p style="color:red;">Error loading items: ' + (data.error || 'Unknown error') + '</p>';
            }
        })
        .catch(err => {
            modalBody.innerHTML = '<p style="color:red;">Error loading items.</p>';
            console.error(err);
        });
}

function closeModal() {
    document.getElementById('orderModal').style.display = "none";
}

// Close if clicked outside
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<style>
.status-select {
    padding: 5px 10px;
    border-radius: 20px;
    border: 1px solid #ddd;
    font-size: 0.85rem;
    cursor: pointer;
}
.status-select.pending { background-color: #fff3cd; color: #856404; }
.status-select.processing { background-color: #cce5ff; color: #004085; }
.status-select.completed { background-color: #d4edda; color: #155724; }
.status-select.cancelled { background-color: #f8d7da; color: #721c24; }
.btn-view { background-color: #17a2b8; color: white; margin-right: 5px; }
</style>

<?php include('../includes/admin_footer.php'); ?>
