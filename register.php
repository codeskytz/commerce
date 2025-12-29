<?php
require('includes/db.php');
// session_start(); handled by header.php but we need it here for logic below
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<?php include('includes/header.php'); ?>

</div> <!-- Close header's main-content container -->

<div class="auth-body">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    
    <div class="glass-card">
        <h2>Register</h2>
        <?php if(isset($error)): ?>
            <p class="error" style="color: var(--danger-color); text-align: center; margin-bottom: 16px;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="register" class="auth-btn">Create Account</button>
        </form>
        
        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-secondary);">
            Already have an account? <a href="login.php" style="color: var(--accent-color); font-weight: 600;">Login here</a>
        </div>
    </div>
</div>

<div class="container"> <!-- Re-open container for footer to close -->
<?php include('includes/footer.php'); ?>
