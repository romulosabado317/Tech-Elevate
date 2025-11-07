    <?php
session_start();
include 'includes/header.php';
?>
 <div class="container text-center" style="display: flex; align-items: center; justify-content: center; min-height: 10vh;">
        <div>
            <img src="/Tech-Elevate-neon/assets/img/logo.png" alt="Tech-Elevate Logo" style="height: 150px; margin-bottom: 1rem;">
            <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 2rem;">Welcome to Tech-Elevate</h1>
            <p class="muted" style="font-size: 1.25rem; max-width: 600px; margin: 0 auto 2.5rem auto;">
                A modern platform for students to showcase their projects and skills.
            </p>
            <div class="form-actions">
                <a href="register.php" class="btn">Get Started</a>
                <a href="login.php" class="btn-outline">Sign In</a>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>