<?php
session_start();
require 'dbconnect.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $course = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $about = trim($_POST['about'] ?? '');

    if ($name === '') $errors[] = 'Name is required';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if ($password === '' || strlen($password) < 6) $errors[] = 'Password at least 6 chars';
    if ($password !== $password2) $errors[] = 'Passwords do not match';
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== 0) $errors[] = 'Profile photo is required';

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) { $errors[] = 'Email already registered'; }
        mysqli_stmt_close($stmt);
    }

    if (empty($errors)) {
        $uploads = 'uploads/photos/';
        if (!is_dir($uploads)) mkdir($uploads, 0755, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('p_') . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploads . $filename);

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'student';
        $status = 'active';
        $stmt = mysqli_prepare($conn, "INSERT INTO users (name,email,password,role,photo,course,year_level,about,status) VALUES (?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "sssssssss", $name, $email, $hash, $role, $filename, $course, $year_level, $about, $status);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($ok) { header('Location: login.php?registered=1'); exit; }
        else { $errors[] = 'Could not create account'; }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="bg-anim"></div>
<div class="particles" aria-hidden="true"><span style="--x:15;--y:25;--s:1"></span><span style="--x:65;--y:15;--s:0.9"></span></div>
<div class="container">
  <div class="center-card neon-card">
    <h2>Create account</h2>
    <?php if(!empty($errors)): ?><div class="error"><ul><?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="form-glass">
      <div class="input"><input name="name" placeholder="Full name" value="<?php echo htmlspecialchars($name ?? '') ?>" required></div>
      <div class="input"><input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email ?? '') ?>" required></div>
      <div class="input"><input type="password" name="password" placeholder="Password" required></div>
      <div class="input"><input type="password" name="password2" placeholder="Confirm password" required></div>
      <div class="input"><input name="course" placeholder="Course" value="<?php echo htmlspecialchars($course ?? '') ?>"></div>
      <div class="input"><input name="year_level" placeholder="Year level" value="<?php echo htmlspecialchars($year_level ?? '') ?>"></div>
      <div class="input"><textarea name="about" placeholder="About"><?php echo htmlspecialchars($about ?? '') ?></textarea></div>
      <div class="input"><label class="file-label">Profile photo (required)<input type="file" name="photo" accept="image/*" required></label></div>
      <button class="btn" type="submit">Create account</button>
    </form>
    <p class="small"><a href="login.php">Back to login</a></p>
  </div>
</div>
</body></html>