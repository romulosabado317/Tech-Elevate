<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
$uid = $_SESSION['user_id'];

// Handle Add Skill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skill_name'])) {
  $skill = trim($_POST['skill_name']);
  $level = $_POST['skill_level'];
  if ($skill !== '') {
    $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, skill_level) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $uid, $skill, $level);
    $stmt->execute();

    // Add to activity log
    $ip = $_SERVER['REMOTE_ADDR'];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $act = "Added new skill: $skill ($level)";
    $log = $conn->prepare("INSERT INTO activity_log (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $log->bind_param("isss", $uid, $act, $ip, $agent);
    $log->execute();
  }
  header("Location: skills.php");
  exit;
}

// Handle Delete Skill
if (isset($_GET['del'])) {
  $sid = (int)$_GET['del'];
  $stmt = $conn->prepare("DELETE FROM skills WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $sid, $uid);
  $stmt->execute();

  // Log delete action
  $ip = $_SERVER['REMOTE_ADDR'];
  $agent = $_SERVER['HTTP_USER_AGENT'];
  $act = "Deleted a skill";
  $log = $conn->prepare("INSERT INTO activity_log (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
  $log->bind_param("isss", $uid, $act, $ip, $agent);
  $log->execute();

  header("Location: skills.php");
  exit;
}

// Fetch Skills
$result = $conn->prepare("SELECT * FROM skills WHERE user_id = ?");
$result->bind_param("i", $uid);
$result->execute();
$skills = $result->get_result();
?>

<?php include 'header.php'; ?>
<div class="container">
  <div class="skills-card">
    <h3>My Skills</h3>
    <form class="skill-form" method="POST">
      <input type="text" name="skill_name" placeholder="Enter skill name" required>
      <select name="skill_level">
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
      </select>
      <button type="submit" class="btn">Add Skill</button>
    </form>

    <div class="skills-list">
      <?php while ($row = $skills->fetch_assoc()): ?>
        <div class="skill-item">
          <span class="skill-name"><?= htmlspecialchars($row['skill_name']) ?></span>
          <span class="skill-level <?= strtolower($row['skill_level']) ?>"><?= htmlspecialchars($row['skill_level']) ?></span>
          <a href="?del=<?= $row['id'] ?>" class="del-skill">&times;</a>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
$uid = $_SESSION['user_id'];

// Handle Add Skill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skill_name'])) {
  $skill = trim($_POST['skill_name']);
  $level = $_POST['skill_level'];
  if ($skill !== '') {
    $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, skill_level) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $uid, $skill, $level);
    $stmt->execute();

    // Add to activity log
    $ip = $_SERVER['REMOTE_ADDR'];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $act = "Added new skill: $skill ($level)";
    $log = $conn->prepare("INSERT INTO activity_log (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $log->bind_param("isss", $uid, $act, $ip, $agent);
    $log->execute();
  }
  header("Location: skills.php");
  exit;
}

// Handle Delete Skill
if (isset($_GET['del'])) {
  $sid = (int)$_GET['del'];
  $stmt = $conn->prepare("DELETE FROM skills WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $sid, $uid);
  $stmt->execute();

  // Log delete action
  $ip = $_SERVER['REMOTE_ADDR'];
  $agent = $_SERVER['HTTP_USER_AGENT'];
  $act = "Deleted a skill";
  $log = $conn->prepare("INSERT INTO activity_log (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
  $log->bind_param("isss", $uid, $act, $ip, $agent);
  $log->execute();

  header("Location: skills.php");
  exit;
}

// Fetch Skills
$result = $conn->prepare("SELECT * FROM skills WHERE user_id = ?");
$result->bind_param("i", $uid);
$result->execute();
$skills = $result->get_result();
?>

<?php include 'header.php'; ?>
<div class="container">
  <div class="skills-card">
    <h3>My Skills</h3>
    <form class="skill-form" method="POST">
      <input type="text" name="skill_name" placeholder="Enter skill name" required>
      <select name="skill_level">
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
      </select>
      <button type="submit" class="btn">Add Skill</button>
    </form>

    <div class="skills-list">
      <?php while ($row = $skills->fetch_assoc()): ?>
        <div class="skill-item">
          <span class="skill-name"><?= htmlspecialchars($row['skill_name']) ?></span>
          <span class="skill-level <?= strtolower($row['skill_level']) ?>"><?= htmlspecialchars($row['skill_level']) ?></span>
          <a href="?del=<?= $row['id'] ?>" class="del-skill">&times;</a>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
