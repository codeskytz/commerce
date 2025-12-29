<?php
require('includes/db.php');
// session_start(); handled by header.php but we need it here for logic below
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<?php
// Header includes session_start and DB connection check via functions.php (if used)
// But we need db.php for the logic above if header doesn't include it. 
// Header includes functions.php. db.php is usually included in functions or separately.
// Let's check header.php content. It requires 'functions.php'. 
// functions.php usually requires db.php? 
// Let's assume we need to keep require('includes/db.php') at top or rely on header.
// Safest is to keep db.php but remove session_start if header does it.
// Actually, header.php does session_start.
include('includes/header.php'); 
?>

</div> <!-- Close header's main-content container to allow full width auth body -->

<div class="auth-body">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    
    <div class="glass-card">
        <h2>Login</h2>
        <?php if(isset($_SESSION['success'])): ?>
            <p class="success" style="color: var(--success-color); text-align: center; margin-bottom: 16px;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <p class="error" style="color: var(--danger-color); text-align: center; margin-bottom: 16px;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="auth-btn">Sign In</button>
        </form>
        
        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-secondary);">
            Don't have an account? <a href="register.php" style="color: var(--accent-color); font-weight: 600;">Register here</a>
        </div>
    </div>
</div>

<div class="container"> <!-- Re-open container for footer to close -->
<?php include('includes/footer.php'); ?>
