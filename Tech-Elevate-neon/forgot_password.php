<?php
include 'includes/header.php';
?>

<div class="center-card form-card">
    <h1>Forgot Password</h1>
    <p class="muted text-center mb-4">Enter your email address and we'll send you a link to reset your password.</p>
    <form action="send_reset_link.php" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn w-100">Send Reset Link</button>
        </div>
    </form>
    <div class="form-footer-link">
        <a href="login.php">Back to Login</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>