<?php
session_start();
require '../dbconnect.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: ../login.php'); exit; }
$res = mysqli_query($conn, "SELECT p.*, u.name FROM projects p JOIN users u ON u.id=p.user_id ORDER BY p.id DESC");
?>
<!doctype html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="sidebar">
  <div class="brand">Tech-Elevate</div>
  <nav><a href="manage_users.php">Users</a><a href="view_projects.php" class="active">Projects</a><a href="activity_log.php">Activity Log</a><a href="about.php">About System</a></nav>
  <div class="sidebar-foot">Group 1 Team Elevate</div>
</div>
<div class="main"><div class="header-bar"><div>All Projects</div><div><a class="btn" href="../logout.php">Logout</a></div></div>
<div class="container"><div class="card-grid"><?php while($p = mysqli_fetch_assoc($res)): ?><div class="card"><h3><?php echo htmlspecialchars($p['project_title']);?></h3><p><?php echo htmlspecialchars($p['project_description']);?></p><p>By: <?php echo htmlspecialchars($p['name']);?></p><?php if($p['filename']): ?><p><a class="btn" href="../uploads/projects/<?php echo htmlspecialchars($p['filename']); ?>" download>Download</a></p><?php endif; ?></div><?php endwhile; ?></div></div></div>
</body></html>