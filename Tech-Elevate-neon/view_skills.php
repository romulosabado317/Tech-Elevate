<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM skills WHERE user_id = $uid ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>My Skills</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<h2>My Skills</h2>
<table border="1" cellpadding="6">
<tr><th>Skill</th><th>Level</th><th>Action</th></tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['skill_name']) ?></td>
<td><?= htmlspecialchars($row['skill_level']) ?></td>
<td><a href="delete_skill.php?id=<?= $row['id'] ?>">🗑</a></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
