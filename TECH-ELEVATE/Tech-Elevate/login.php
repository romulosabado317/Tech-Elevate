<?php
session_start();
require 'dbconnect.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if ($user) {
        if (password_verify($password, $user['password']) || $user['password'] === md5($password)) {
            $_SESSION['user'] = $user;
            echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
            echo '<link rel="stylesheet" href="assets/css/style.css">';
            echo '</head><body><div class="loader-wrap"><div class="loader">Tech-Elevate</div></div>';
            echo '<script>setTimeout(function(){ window.location = "' . ($user['role'] === 'admin' ? 'admin/manage_users.php' : 'student/dashboard.php') . '"; }, 1800);</script></body></html>';
            exit;
        } else { $error = 'Invalid credentials'; }
    } else { $error = 'Invalid credentials'; }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tech-Elevate — Login</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="bg-anim"></div>
<div class="particles" aria-hidden="true">
  <span style="--x:10;--y:20;--s:0.8"></span>
  <span style="--x:80;--y:10;--s:1.2"></span>
  <span style="--x:40;--y:80;--s:0.9"></span>
  <span style="--x:70;--y:60;--s:0.6"></span>
  <span style="--x:20;--y:40;--s:1.1"></span>
</div>
<div class="container">
  <div class="center-card neon-card">
    <div class="logo"><div class="mark">T</div><h1>Tech Elevate</h1></div>
    <h2>Sign in</h2>
    <?php if($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" class="form-glass">
      <div class="input"><input type="email" name="email" placeholder="Email" required></div>
      <div class="input"><input type="password" name="password" placeholder="Password" required></div>
      <button class="btn" type="submit">Sign in</button>
    </form>
    <p class="small"><a href="register.php">Register</a> • <a href="forgot_password.php">Forgot?</a></p>
  </div>
</div>
</body></html>