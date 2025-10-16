<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require '../dbconnect.php';
if (!isset($_SESSION['user'])) { header('Location: ../login.php'); exit; }
$user = $_SESSION['user'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $about = trim($_POST['about'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '') $errors[] = 'Name required';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        if ($_FILES['photo']['size'] > 10 * 1024 * 1024) $errors[] = 'Photo too large';
        else {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('p_') . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/photos/' . $filename);
            $photo_sql = ", photo = '".mysqli_real_escape_string($conn, $filename)."'";
        }
    } else { $photo_sql = ''; }
    if (empty($errors)) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ?, course = ?, year_level = ?, about = ?, password = ?".$photo_sql." WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssssssi", $name, $email, $course, $year_level, $about, $hash, $user['id']);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ?, course = ?, year_level = ?, about = ?".$photo_sql." WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $course, $year_level, $about, $user['id']);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $stmt2 = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt2, "i", $user['id']);
        mysqli_stmt_execute($stmt2);
        $res = mysqli_stmt_get_result($stmt2);
        $_SESSION['user'] = mysqli_fetch_assoc($res);
        header('Location: profile.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-anim"></div>
<div class="container">
    <div class="center-card neon-card">
        <h2>Edit Profile</h2>
        <?php if(!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?>
            </ul>
        </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="form-glass">
            <div class="input">
                <input name="name" placeholder="Name" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="input">
                <input name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="input">
                <input type="password" name="password" placeholder="New password (leave blank)">
            </div>
            <div class="input">
                <input name="course" placeholder="Course" value="<?php echo htmlspecialchars($user['course'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="input">
                <input name="year_level" placeholder="Year level" value="<?php echo htmlspecialchars($user['year_level'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="input">
                <textarea name="about" placeholder="About"><?php echo htmlspecialchars($user['about'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div class="input">
                <label class="file-label">Change photo
                    <input type="file" name="photo" accept="image/*">
                </label>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Save changes</button>
                <a href="dashboard.php" class="btn" style="background-color:#4A90E2; text-decoration:none;">Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
