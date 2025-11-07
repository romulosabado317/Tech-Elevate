<?php
require 'db_connect.php';

$email = $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $otp = $conn->real_escape_string($_POST['otp']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $res = $conn->query("SELECT * FROM users WHERE email='$email' AND reset_token='$otp' AND reset_expires > NOW() LIMIT 1");
        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password = '$hashed_password', reset_token = NULL, reset_expires = NULL WHERE id = " . $user['id']);
            header('Location: login.php?reset=success');
            exit;
        } else {
            $error = "Invalid or expired OTP.";
        }
    }
}

include 'includes/header.php';
?>

<div class="center-card form-card">
    <h1>Reset Password</h1>
    <?php if (isset($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
    <form method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <div class="form-group">
            <label for="otp">Enter OTP</label>
            <input type="text" name="otp" id="otp" required>
        </div>
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn w-100">Reset Password</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>