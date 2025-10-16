<?php
session_start();
require '../dbconnect.php';
if (!isset($_SESSION['user'])) { header('Location: ../login.php'); exit; }
$user = $_SESSION['user'];
?>
<!doctype html><html><head><link rel="stylesheet" href="../assets/css/style.css"><script src="../assets/js/script.js"></script></head><body>
<div class="bg-anim"></div>
<div class="header-bar"><div class="brand">Tech-Elevate</div><div><a class="btn" href="../logout.php">Logout</a></div></div>
<div class="loader-welcome" id="welcome"><div class="welcome-text">Welcome, <?php echo htmlspecialchars($user['name']); ?> ✨</div></div>
<div class="container">
  <div class="grid">
    <div class="profile-card neon-card">
      <?php if($user['photo']): ?><img src="../uploads/photos/<?php echo htmlspecialchars($user['photo']); ?>" class="profile-photo"><?php endif; ?>
      <h3><?php echo htmlspecialchars($user['name']); ?></h3>
      <p class="small"><?php echo htmlspecialchars($user['course']); ?> • <?php echo htmlspecialchars($user['year_level']); ?></p>
      <p><?php echo nl2br(htmlspecialchars($user['about'])); ?></p>
      <p><a class="btn" href="profile.php">View Profile</a></p>
    </div>
    <div class="actions-card neon-card">
      <h3>Your Projects</h3>
      <p><a class="btn" href="upload_project.php">Upload Project</a> <a class="btn" href="projects.php">Manage Projects</a></p>
    </div>
  </div>
</div>
<script>setTimeout(()=>{document.getElementById('welcome').style.opacity=0},2200);</script>
</body></html>