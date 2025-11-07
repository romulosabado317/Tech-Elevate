<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$uid = (int)$_SESSION['user_id'];
$result = $conn->query("SELECT * FROM skills WHERE user_id=$uid ORDER BY created_at DESC");

include 'includes/header.php';
?>
<div class="container">
  <h2>Your Skills</h2>
  <a href="skills_add.php" class="btn">+ Add New Skill</a>
  <table border="1" cellpadding="8">
    <tr><th>Skill</th><th>Level</th><th>Action</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['skill_name']) ?></td>
        <td><?= htmlspecialchars($row['skill_level']) ?></td>
        <td><a href="skills_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this skill?');">Delete</a></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <br>
  <a href="dashboard.php" class="btn-outline">Back to Dashboard</a>
</div>
<?php include 'includes/footer.php'; ?>
