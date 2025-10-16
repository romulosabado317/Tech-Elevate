<?php
session_start();
require '../dbconnect.php';
if (!isset($_SESSION['user'])) { header('Location: ../login.php'); exit; }
$user = $_SESSION['user'];
$stmt = mysqli_prepare($conn, "SELECT * FROM projects WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!doctype html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="bg-anim"></div>
<div class="container"><h2>Your Projects</h2><div class="card-grid"><?php while($p = mysqli_fetch_assoc($result)): ?><div class="card"><h3><?php echo htmlspecialchars($p['project_title']); ?></h3><p><?php echo htmlspecialchars($p['project_description']); ?></p><?php if($p['filename']): ?><p><a href="../uploads/projects/<?php echo htmlspecialchars($p['filename']); ?>" download class="btn">Download</a></p><?php endif; ?></div><?php endwhile; ?></div></div></body></html>