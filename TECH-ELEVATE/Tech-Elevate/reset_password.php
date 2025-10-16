<?php
session_start();
require 'dbconnect.php';
$msg=''; $error='';
$token = $_GET['token'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) $error='Password too short';
    else {
        $stmt = mysqli_prepare($conn, "SELECT id, reset_expires FROM users WHERE reset_token = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $u = mysqli_fetch_assoc($res);
        if (!$u) $error='Invalid token';
        elseif (strtotime($u['reset_expires']) < time()) $error='Token expired';
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = mysqli_prepare($conn, "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            mysqli_stmt_bind_param($stmt2, "si", $hash, $u['id']);
            mysqli_stmt_execute($stmt2);
            $msg='Password reset successfully';
        }
    }
}
?>
<!doctype html><html><head><link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="bg-anim"></div>
<div class="container"><div class="center-card neon-card"><h2>Reset password</h2><?php if($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?><?php if($msg): ?><div class="small"><?php echo $msg; ?></div><?php endif; ?><form method="post" class="form-glass"><input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>"><div class="input"><input type="password" name="password" placeholder="New password" required></div><button class="btn" type="submit">Set new password</button></form></div></div></body></html>