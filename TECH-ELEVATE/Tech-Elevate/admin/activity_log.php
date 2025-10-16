<?php
session_start();
require '../dbconnect.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: ../login.php'); exit; }
$res = mysqli_query($conn, "SELECT al.*, u.name FROM activity_log al LEFT JOIN users u ON u.id=al.user_id ORDER BY al.id DESC");
?>
<!doctype html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="sidebar">
  <div class="brand">Tech-Elevate</div>
  <nav><a href="manage_users.php">Users</a><a href="view_projects.php">Projects</a><a href="activity_log.php" class="active">Activity Log</a><a href="about.php">About System</a></nav>
  <div class="sidebar-foot">Group 1 Team Elevate</div>
</div>
<div class="main"><div class="header-bar"><div>Activity Log</div><div><a class="btn" href="../logout.php">Logout</a></div></div>
<div class="container"><table style='width:100%;color:#eaf0ff'><thead><tr><th>User</th><th>Action</th><th>Date</th></tr></thead><tbody><?php while($r=mysqli_fetch_assoc($res)): ?><tr><td><?php echo htmlspecialchars($r['name']); ?></td><td><?php echo htmlspecialchars($r['action']); ?></td><td><?php echo htmlspecialchars($r['date_time']); ?></td></tr><?php endwhile; ?></tbody></table></div></div>
</body></html>