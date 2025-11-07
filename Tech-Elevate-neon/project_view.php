<?php
session_start();
require 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get project ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: explore.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch project details
$stmt = $conn->prepare("
    SELECT p.*, u.name AS uploader
    FROM projects p
    JOIN users u ON u.id = p.user_id
    WHERE p.id = ? AND p.status = 'active'
    LIMIT 1
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    include 'includes/header.php';
    echo "<div class='container'><h2>Project not found</h2>
          <p><a class='btn-outline' href='explore.php'>Back to Explore</a></p></div>";
    include 'includes/footer.php';
    exit;
}

$p = $result->fetch_assoc();
include 'includes/header.php';
?>

<div class="container">
  <div class="project-view-card">
    <h2><?php echo htmlspecialchars($p['project_title']); ?></h2>
    <p class="muted">by <?php echo htmlspecialchars($p['uploader']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($p['project_description'])); ?></p>
    <hr>

    <?php if ($p['filename']): ?>
      <div class="file-preview">
        <?php
        $files = explode(',', $p['filename']);
        foreach ($files as $file):
            $file = trim($file);
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $filePath = "uploads/" . $file;

            if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                echo "<img src='$filePath' alt='Preview' class='preview-img'>";
            } elseif ($ext === 'pdf') {
                echo "<iframe src='$filePath' class='preview-pdf'></iframe>";
            } else {
                echo "<a href='$filePath' class='download-link' download>📎 Download " . htmlspecialchars($file) . "</a><br>";
            }
        endforeach;
        ?>
      </div>
    <?php else: ?>
      <p class="muted">No files uploaded for this project.</p>
    <?php endif; ?>

    <div class="form-actions">
      <a class="btn-outline" href="explore.php">← Back to Explore</a>
    </div>
  </div>
</div>

<style>
.container {
  max-width: 800px;
  margin: 40px auto;
  color: #fff;
  text-align: center;
}
.project-view-card {
  background: rgba(20,20,30,0.8);
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 0 20px rgba(0,255,255,0.3);
}
.preview-img {
  max-width: 100%;
  border-radius: 10px;
  margin: 15px 0;
  box-shadow: 0 0 15px rgba(0,255,255,0.2);
}
.preview-pdf {
  width: 100%;
  height: 500px;
  border: none;
  margin: 15px 0;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0,255,255,0.2);
}
.download-link {
  color: #0ff;
  text-decoration: none;
  font-weight: bold;
}
.download-link:hover {
  text-decoration: underline;
}
.btn-outline {
  display: inline-block;
  padding: 8px 20px;
  border: 1px solid #0ff;
  border-radius: 8px;
  color: #0ff;
  text-decoration: none;
  transition: 0.3s;
}
.btn-outline:hover {
  background: #0ff;
  color: #000;
}
.muted {
  color: #aaa;
  font-size: 14px;
}
</style>

<?php include 'includes/footer.php'; ?>
