<?php
session_start();
require '../dbconnect.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$user = $_SESSION['user'];
$view_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user['role'] === 'admin' && $view_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $view_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($res);
    if (!$student) { echo "Student not found"; exit; }
} else {
    $student = $user;
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
        <h2>Profile</h2>
        <div style="text-align:center;">
            <?php if(!empty($student['photo'])): ?>
                <img src="../uploads/photos/<?php echo htmlspecialchars($student['photo']); ?>" alt="Photo" style="max-width:140px;border-radius:50%;">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($student['name']); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course']); ?></p>
            <p><strong>Year level:</strong> <?php echo htmlspecialchars($student['year_level']); ?></p>
            <p><strong>About:</strong><br><?php echo nl2br(htmlspecialchars($student['about'])); ?></p>
            <div style="margin-top:12px;">
                <?php if($user['role'] === 'admin'): ?>
                    <a href="../admin/edit_student.php?id=<?php echo intval($student['id']); ?>" class="btn">Edit Student</a>
                <?php else: ?>
                    <a href="profile_edit.php" class="btn">Edit Profile</a>
                <?php endif; ?>
                <a href="../dashboard.php" class="btn">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
