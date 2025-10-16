<?php
session_start();
require '../dbconnect.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$user = $_SESSION['user'];
// If admin viewing a specific user? redirect to admin edit page instead
if ($user['role'] === 'admin' && isset($_GET['id'])) {
    header('Location: ../admin/edit_student.php?id=' . intval($_GET['id']));
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Profile - Tech-Elevate</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-anim"></div>
<div class="container">
    <div class="center-card neon-card">
        <h2>My Profile</h2>
        <div style="text-align:center;">
            <?php if(!empty($user['photo'])): ?>
                <img src="../uploads/photos/<?php echo htmlspecialchars($user['photo']); ?>" alt="Photo" style="max-width:140px;border-radius:50%;">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($user['course']); ?></p>
            <p><strong>Year level:</strong> <?php echo htmlspecialchars($user['year_level']); ?></p>
            <p><strong>About:</strong><br><?php echo nl2br(htmlspecialchars($user['about'])); ?></p>
            <div style="margin-top:12px;">
                <a href="profile_edit.php" class="btn">Edit Profile</a>
                <a href="../dashboard.php" class="btn">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
