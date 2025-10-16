<?php
session_start();
require '../dbconnect.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: ../login.php'); exit; }

if (isset($_GET['delete'])) { $id = (int)$_GET['delete']; $stmt = mysqli_prepare($conn, "UPDATE users SET status='inactive' WHERE id = ?"); mysqli_stmt_bind_param($stmt, "i", $id); mysqli_stmt_execute($stmt); header('Location: manage_users.php'); exit; }
if (isset($_GET['restore'])) { $id = (int)$_GET['restore']; $stmt = mysqli_prepare($conn, "UPDATE users SET status='active' WHERE id = ?"); mysqli_stmt_bind_param($stmt, "i", $id); mysqli_stmt_execute($stmt); header('Location: manage_users.php'); exit; }
if (isset($_GET['resetpw'])) { $id = (int)$_GET['resetpw']; $new = 'Students123'; $hash = password_hash($new, PASSWORD_DEFAULT); $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?"); mysqli_stmt_bind_param($stmt, "si", $hash, $id); mysqli_stmt_execute($stmt); header('Location: manage_users.php'); exit; }

$res = mysqli_query($conn, "SELECT id,name,email,role,course,year_level,status,photo FROM users ORDER BY id DESC");
?>
<!doctype html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="sidebar">
  <div class="brand">Tech-Elevate</div>
  <nav><a href="manage_users.php" class="active">Users</a><a href="view_projects.php">Projects</a><a href="activity_log.php">Activity Log</a><a href="about.php">About System</a></nav>
  <div class="sidebar-foot">Group 1 Team Elevate</div>
</div>
<div class="main"><div class="header-bar"><div>Admin - Users</div><div><a class="btn" href="../logout.php">Logout</a></div></div>
<div class="container">
  <h2>All users</h2>
  <table style="width:100%;border-collapse:collapse;color:#e6eef8">
    <thead><tr><th>Photo</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while($u = mysqli_fetch_assoc($res)): ?>
      <tr style="border-bottom:1px solid rgba(255,255,255,0.04)">
        <td><?php if($u['photo']): ?><img src="../uploads/photos/<?php echo htmlspecialchars($u['photo']); ?>" style="width:48px;border-radius:6px"><?php endif; ?></td>
        <td><?php echo htmlspecialchars($u['name']); ?></td>
        <td><?php echo htmlspecialchars($u['email']); ?></td>
        <td><?php echo htmlspecialchars($u['course']); ?></td>
        <td><?php echo htmlspecialchars($u['year_level']); ?></td>
        <td><?php echo htmlspecialchars($u['status']); ?></td>
        <td>
          <a class="btn" href="manage_users.php?delete=<?php echo $u['id']; ?>">Delete</a>
          <a class="btn" href="manage_users.php?restore=<?php echo $u['id']; ?>">Restore</a>
          <a class="btn" href="manage_users.php?resetpw=<?php echo $u['id']; ?>">Reset PW</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div></div>
</body></html>