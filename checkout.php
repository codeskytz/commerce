<?php
require('includes/db.php');
require('includes/functions.php');
include('includes/header.php');

$isLoggedIn = isset($_SESSION['user_id']);

// Get WhatsApp number from settings
$whatsapp_number = getSetting('whatsapp_number', '+255123456789');
?>

<!-- Cart Content -->
<?php if ($isLoggedIn): ?>

<div class="container">
    <h2 style="margin: 20px 0;">Checkout</h2>
    
    <div id="empty-cart" style="text-align: center; padding: 60px 20px; display: none;">
        <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ddd; margin-bottom: 16px;"></i>
        <h3 style="color: var(--text-secondary);">Your cart is empty</h3>
        <p style="color: var(--text-secondary); margin-bottom: 24px;">Add items to get started!</p>
        <a href="index.php" class="btn">Browse Products</a>
    </div>
    
    <div id="checkout-content" style="display: none;">
        <!-- Cart Summary Section -->
        <div class="dashboard-card" style="margin-bottom: 20px;">
            <h3>Order Summary</h3>
            <div id="cart-items-display" class="cart-list">
                <!-- Cart items will be rendered here by JavaScript -->
            </div>
            
            <div id="cart-summary" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--border-color);">
                <!-- Summary will be rendered here -->
            </div>
        </div>
        
        <!-- Delivery Information Form -->
        <div class="dashboard-card">
            <h3>Delivery Information</h3>
            <form id="whatsapp-checkout-form">
                <div class="checkout-form-group">
                    <label>Full Name *</label>
                    <div style="position: relative;">
                        <i class="fas fa-user" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="customer-name" class="checkout-input" style="padding-left: 48px;" placeholder="Enter your full name" required>
                    </div>
                </div>
                
                <div class="checkout-form-group">
                    <label>Phone Number *</label>
                    <div style="position: relative;">
                        <i class="fas fa-phone" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="tel" id="customer-phone" class="checkout-input" style="padding-left: 48px;" placeholder="+255 123 456 789" required>
                    </div>
                </div>
                
                <div class="checkout-form-group">
                    <label>Delivery Address *</label>
                    <div style="position: relative;">
                        <i class="fas fa-map-marker-alt" style="position: absolute; left: 16px; top: 20px; color: #94a3b8;"></i>
                        <textarea id="customer-address" class="checkout-input" rows="3" style="padding-left: 48px;" placeholder="Street, City, Region" required></textarea>
                    </div>
                </div>
                
                <div class="checkout-form-group">
                    <label>Additional Notes (Optional)</label>
                    <div style="position: relative;">
                        <i class="fas fa-sticky-note" style="position: absolute; left: 16px; top: 20px; color: #94a3b8;"></i>
                        <textarea id="customer-notes" class="checkout-input" rows="2" style="padding-left: 48px;" placeholder="Special instructions, delivery time preferences, etc."></textarea>
                    </div>
                </div>
                
                <div class="whatsapp-summary-box">
                    <div class="whatsapp-icon-large">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div>
                        <strong style="font-size: 1.1rem; color: #075e54;">WhatsApp Checkout</strong>
                        <p style="font-size: 0.9rem; color: #556c69; margin: 4px 0 0 0; line-height: 1.4;">
                            We'll send your order details directly to our WhatsApp for quick confirmation and support.
                        </p>
                    </div>
                </div>
                
                <button type="submit" class="whatsapp-btn-submit">
                    <i class="fab fa-whatsapp" style="font-size: 1.4rem;"></i>
                    <span>Place Order via WhatsApp</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const WHATSAPP_NUMBER = '<?php echo $whatsapp_number; ?>';

console.log('Checkout inline script loaded');

// WhatsApp form handler
setTimeout(function() {
    const form = document.getElementById('whatsapp-checkout-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('customer-name').value.trim();
            const phone = document.getElementById('customer-phone').value.trim();
            const address = document.getElementById('customer-address').value.trim();
            const notes = document.getElementById('customer-notes').value.trim();
            
            if (!name || !phone || !address) {
                showToast('Please fill in all required fields', 'error');
                return;
            }
            
            // Format WhatsApp message
            let message = `🛒 *NEW ORDER*\n\n`;
            message += `👤 *Customer Details:*\n`;
            message += `Name: ${name}\n`;
            message += `Phone: ${phone}\n`;
            message += `Address: ${address}\n\n`;
            
            message += `📦 *Order Items:*\n`;
            message += `━━━━━━━━━━━━━━━━━━\n`;
            
            let subtotal = 0;
            let totalShipping = 0;
            
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                const itemShipping = (item.shipping_fee || 0) * item.quantity;
                totalShipping += itemShipping;
                
                message += `${index + 1}. ${item.name}\n`;
                message += `   Qty: ${item.quantity} × ${CURRENCY_SYMBOL}${parseFloat(item.price).toFixed(2)}\n`;
                message += `   Subtotal: ${CURRENCY_SYMBOL}${itemTotal.toFixed(2)}\n\n`;
            });
            
            message += `━━━━━━━━━━━━━━━━━━\n`;
            message += `💰 *Subtotal:* ${CURRENCY_SYMBOL}${subtotal.toFixed(2)}\n`;
            message += `🚚 *Shipping:* ${CURRENCY_SYMBOL}${totalShipping.toFixed(2)}\n`;
            message += `━━━━━━━━━━━━━━━━━━\n`;
            message += `✨ *TOTAL:* ${CURRENCY_SYMBOL}${(subtotal + totalShipping).toFixed(2)}\n\n`;
            
            if (notes) {
                message += `📝 *Additional Notes:*\n${notes}\n\n`;
            }
            
            message += `⏰ Order placed: ${new Date().toLocaleString()}\n`;
            message += `━━━━━━━━━━━━━━━━━━\n`;
            message += `Thank you for your order! 🙏`;
            
            // Encode message for URL
            const encodedMessage = encodeURIComponent(message);
            
            // Create WhatsApp URL
            const whatsappURL = `https://wa.me/${WHATSAPP_NUMBER.replace(/[^0-9]/g, '')}?text=${encodedMessage}`;
            
            // Save order to database
            saveOrderToDatabase(name, phone, address, notes, subtotal + totalShipping);
            
            // Open WhatsApp
            window.open(whatsappURL, '_blank');
            
            // Clear cart after 2 seconds
            setTimeout(() => {
                cart = [];
                saveCart();
                showToast('Order sent! Check WhatsApp to confirm.', 'success');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            }, 2000);
        });
    }
}, 500);

// Save order to database
function saveOrderToDatabase(name, phone, address, notes, total) {
    const orderData = {
        customer_name: name,
        customer_phone: phone,
        customer_address: address,
        notes: notes,
        total_amount: total,
        items: JSON.stringify(cart)
    };
    
    fetch('api/save_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Order saved:', data);
    })
    .catch(error => {
        console.error('Error saving order:', error);
    });
}


</script>

<?php else: ?>
    <div style="text-align: center; padding: 20px;">
        <h3>Please Login</h3>
        <p>You need to be logged in to complete the purchase.</p>
        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 16px;">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn" style="background: var(--text-secondary);">Register</a>
        </div>
    </div>
<?php endif; ?>

<?php include('includes/footer.php'); ?>
