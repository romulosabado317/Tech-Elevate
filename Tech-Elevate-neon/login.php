<?php
session_start();
require 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND status='active' LIMIT 1");
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
        $dbpass = $user['password'];
        $ok = false;
        if (password_verify($password, $dbpass)) $ok = true;
        if (md5($password) === $dbpass) $ok = true;
        if ($ok) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        }
    }
    $error = 'Invalid credentials.';
}
include 'includes/header.php';
?>
<div class="page-body">
    <div class="center-card form-card">
        <h2>Sign In</h2>
        <?php 
        if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
            echo "<script>\n            if (window.history.replaceState) {\n                window.history.replaceState({}, document.title, window.location.pathname);\n            }\n            Swal.fire({\n              title: 'Success!',\n              text: 'Your password has been changed successfully.',\n              icon: 'success',\n              confirmButtonText: 'OK'\n            });\n            </script>";
        }
        if (isset($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; 
        ?>
        <form method="post" novalidate>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-actions">
                 <button type="submit" class="btn w-100">Sign In</button>
            </div>
        </form>
        <div class="form-footer-link">
            <a href="forgot_password.php">Forgot Password?</a>
            <p>Don't have an account? <a href="register.php">Sign Up</a></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>