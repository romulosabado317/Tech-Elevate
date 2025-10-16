<?php
session_start();
require '../dbconnect.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$errors = [];
$admin = $_SESSION['user'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Invalid student ID."; exit;
}

// Fetch student
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? AND role = 'student' LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($res);
if (!$student) {
    echo "Student not found."; exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $about = trim($_POST['about'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') $errors[] = 'Name required';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';

    $photo_sql = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        if ($_FILES['photo']['size'] > 10 * 1024 * 1024) {
            $errors[] = 'Photo too large (max 10 MB)';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);
            $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
            if (!isset($allowed[$mime])) {
                $errors[] = 'Invalid photo type';
            } else {
                $ext = $allowed[$mime];
                $upload_dir = __DIR__ . '/../uploads/photos';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                $filename = uniqid('p_') . '.' . $ext;
                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir.'/'.$filename)) {
                    $errors[] = 'Failed to move uploaded file';
                } else {
                    $photo_sql = ", photo = '".mysqli_real_escape_string($conn, $filename)."'";
                }
            }
        }
    }

    if (empty($errors)) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name=?, email=?, course=?, year_level=?, about=?, password=? $photo_sql WHERE id=?";
            $stmt2 = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt2, "ssssssi", $name, $email, $course, $year_level, $about, $hash, $id);
        } else {
            $sql = "UPDATE users SET name=?, email=?, course=?, year_level=?, about=? $photo_sql WHERE id=?";
            $stmt2 = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt2, "sssssi", $name, $email, $course, $year_level, $about, $id);
        }
        if ($stmt2) {
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
            // log activity
            $act = mysqli_prepare($conn, "INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
            $action = "Admin {$admin['id']} updated student {$id}";
            mysqli_stmt_bind_param($act, "is", $admin['id'], $action);
            mysqli_stmt_execute($act);
            mysqli_stmt_close($act);

            header('Location: ../student/profile.php?id='.$id);
            exit;
        } else {
            $errors[] = 'DB error: '.mysqli_error($conn);
        }
    }
    // refresh student data for display
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($res);
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Student - Admin - Tech-Elevate</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-anim"></div>
<div class="container">
    <div class="center-card neon-card">
        <h2>Edit Student (Admin)</h2>
        <?php if(!empty($errors)): ?>
            <div class="error"><ul><?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="form-glass">
            <div class="input"><input name="name" placeholder="Name" value="<?php echo htmlspecialchars($student['name']); ?>" required></div>
            <div class="input"><input name="email" placeholder="Email" value="<?php echo htmlspecialchars($student['email']); ?>" required></div>
            <div class="input"><input type="password" name="password" placeholder="New password (leave blank)"></div>
            <div class="input"><input name="course" placeholder="Course" value="<?php echo htmlspecialchars($student['course']); ?>"></div>
            <div class="input"><input name="year_level" placeholder="Year level" value="<?php echo htmlspecialchars($student['year_level']); ?>"></div>
            <div class="input"><textarea name="about" placeholder="About"><?php echo htmlspecialchars($student['about']); ?></textarea></div>
            <div class="input"><label class="file-label">Change photo<input type="file" name="photo" accept="image/*"></label></div>

            <div style="display:flex; gap:10px; align-items:center;">
                <button class="btn" type="submit">Save changes</button>
                <a href="../dashboard.php" class="btn">Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
