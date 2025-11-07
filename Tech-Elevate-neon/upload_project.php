<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $uploadDir = 'uploads';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileName = '';
    if (!empty($_FILES['file']['name'])) {
        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip'];
        $maxSize = 20 * 1024 * 1024; // 20MB

        if (in_array($ext, $allowedExt) && $file['size'] <= $maxSize) {
            $uniqueName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target = $uploadDir . '/' . $uniqueName;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $fileName = $uniqueName;
            }
        } else {
            $error = 'Invalid file type or size too large.';
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO projects (user_id, project_title, project_description, filename, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->bind_param('isss', $uid, $title, $desc, $fileName);
        if ($stmt->execute()) {
            $success = 'Project uploaded successfully!';
        } else {
            $error = 'Database error. Please try again.';
        }
    }
}

include 'includes/header.php';
?>
<div class="container">
    <div class="form-card" style="max-width: 700px; margin: auto;">
        <h2 style="margin-bottom: 1.5rem;">Upload New Project</h2>
        
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom: 1rem;">
                <label for="title">Project Title</label>
                <input id="title" name="title" required>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label for="description">Project Description</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label for="file">Project File (Image, PDF, DOC, ZIP)</label>
                <input type="file" id="file" name="file">
            </div>
            <div class="form-actions">
                <a class="btn-outline" href="dashboard.php">Back to Dashboard</a>
                <button type="submit" class="btn">Upload Project</button>
            </div>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>