<?php
session_start();
require 'dbconnect.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
$user = $_SESSION['user'];
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Dashboard</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="bg-anim"></div><div class="container"><div class="center-card neon-card"><h2>Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($user['name']); ?></p>
<ul style="list-style:none;padding:0;">
<li><a href="student/profile_view.php" class="btn">My Profile</a></li>
<?php if($user['role'] === 'admin'): ?>
<li><a href="admin/edit_student.php?id=2" class="btn">Edit Student #2 (example)</a></li>
<?php endif; ?>
<li><a href="logout.php" class="btn">Logout</a></li>
</ul>
</div></div></body></html>