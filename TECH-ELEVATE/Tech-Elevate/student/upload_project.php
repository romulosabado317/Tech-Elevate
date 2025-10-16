<?php
session_start();
require '../dbconnect.php';
if (!isset($_SESSION['user'])) { header('Location: ../login.php'); exit; }
$user = $_SESSION['user'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($title === '') $errors[] = 'Title required';
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) $errors[] = 'File required';
    if (empty($errors)) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('proj_') . '.' . $ext;
        move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/projects/' . $filename);
        $stmt = mysqli_prepare($conn, "INSERT INTO projects (user_id, project_title, project_description, filename) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "isss", $user['id'], $title, $desc, $filename);
        mysqli_stmt_execute($stmt);
        header('Location: projects.php');
        exit;
    }
}
?>
<!doctype html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="bg-anim"></div>
<div class="container"><div class="center-card neon-card"><h2>Upload Project</h2><?php if(!empty($errors)): ?><div class="error"><?php echo htmlspecialchars(implode(', ',$errors)); ?></div><?php endif; ?><form method="post" enctype="multipart/form-data" class="form-glass"><div class="input"><input name="title" placeholder="Project title"></div><div class="input"><textarea name="description" placeholder="Description"></textarea></div><div class="input"><label class="file-label">Project file<input type="file" name="file" required></label></div><button class="btn" type="submit">Upload</button></form></div></div></body></html>