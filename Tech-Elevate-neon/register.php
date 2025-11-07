<?php
session_start();
require 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $r = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($r->num_rows > 0) {
        $error = 'Email already registered.';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,status) VALUES (?,?,?, 'student', 'active')");
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = 'student';
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Registration failed.';
        }
    }
}
include 'includes/header.php';
?>
<div class="page-body">
    <div class="center-card form-card">
        <h2>Create Your Account</h2>
        <?php if (isset($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
        <form method="post" novalidate>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn w-100">Create Account</button>
            </div>
        </form>
        <div class="form-footer-link">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>