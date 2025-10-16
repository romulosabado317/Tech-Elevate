<?php
session_start();
require 'dbconnect.php';
$msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', time() + 15*60);
    $stmt = mysqli_prepare($conn, "UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $email);
    mysqli_stmt_execute($stmt);
    $msg = "If that email exists, a reset link was generated.<br>Reset link (local): <a href='reset_password.php?token=$token'>Reset password</a>";
}
?>
<!doctype html><html><head><link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="bg-anim"></div>
<div class="container"><div class="center-card neon-card"><h2>Forgot password</h2><?php if($msg): ?><div class="small"><?php echo $msg; ?></div><?php endif; ?><form method="post" class="form-glass"><div class="input"><input type="email" name="email" placeholder="Email" required></div><button class="btn" type="submit">Send reset link</button></form></div></div></body></html>