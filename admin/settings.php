<?php
require_once '../includes/functions.php';
include('../includes/admin_header.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title = $_POST['site_title'];
    $whatsapp_number = $_POST['whatsapp_number'];
    $currency_symbol = $_POST['currency_symbol'];
    
    updateSetting('site_title', $site_title);
    updateSetting('whatsapp_number', $whatsapp_number);
    updateSetting('currency_symbol', $currency_symbol);
    
    $message = "Settings updated successfully!";
}

$site_title = getSetting('site_title', 'JJ.MOBISHOP');
$whatsapp_number = getSetting('whatsapp_number', '+255123456789');
$currency_symbol = getSetting('currency_symbol', 'TZS');
?>

<h2>Site Settings</h2>

<?php if ($message): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="dashboard-card" style="max-width: 600px;">
    <form method="POST">
        <div class="form-group">
            <label>Site Title</label>
            <input type="text" name="site_title" value="<?php echo htmlspecialchars($site_title); ?>" required>
        </div>
        
        <div class="form-group">
            <label>WhatsApp Business Number</label>
            <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($whatsapp_number); ?>" placeholder="+255123456789" required>
            <small style="color: var(--text-secondary); font-size: 0.85rem; display: block; margin-top: 4px;">
                Format: +countrycode + number (e.g., +255712345678). Used for WhatsApp checkout.
            </small>
        </div>
        
        <div class="form-group">
            <label>Currency Symbol</label>
            <input type="text" name="currency_symbol" value="<?php echo htmlspecialchars($currency_symbol); ?>" placeholder="TZS" required>
            <small style="color: var(--text-secondary); font-size: 0.85rem; display: block; margin-top: 4px;">
                E.g., $, €, TZS, KES, etc. Used for price display throughout the site.
            </small>
        </div>
        
        <button type="submit" class="btn">Save Settings</button>
    </form>
</div>

<?php include('../includes/admin_footer.php'); ?>
