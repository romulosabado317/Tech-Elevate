<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'config.php';
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);

    // Check if email exists
    $res = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // Generate OTP
        $otp = rand(100000, 999999);
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Save OTP to database
        $conn->query("UPDATE users SET reset_token = '$otp', reset_expires = '$otp_expiry' WHERE id = " . $user['id']);

        // Send email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port       = SMTP_PORT;

            //Recipients
            $mail->setFrom('Tyranorex37@gmail.com', 'Tech-Elevate');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Your OTP for password reset is: <b>$otp</b>. It will expire in 15 minutes.";

            $mail->send();
            header('Location: reset_password.php?email=' . urlencode($email));
            exit;
        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "No user found with that email address.";
    }
}

include 'includes/header.php';
?>

<div class="center-card form-card">
    <h1>Forgot Password</h1>
    <?php if (isset($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
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