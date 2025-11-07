<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
$skills_res = $conn->query("SELECT * FROM skills WHERE user_id=$uid");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $course = $conn->real_escape_string($_POST['course']);
    $year = $conn->real_escape_string($_POST['year_level']);
    $about = $conn->real_escape_string($_POST['about']);
    $photo = $user['photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $d = 'uploads/profile_photos';
        if (!is_dir($d)) mkdir($d, 0755, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $fn = time() . "_" . uniqid() . "." . $ext;
        $target = $d . '/' . $fn;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target);
        $photo = $conn->real_escape_string($fn);
    }

    $stmt = $conn->prepare("UPDATE users SET name=?, course=?, year_level=?, about=?, photo=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param('sssssi', $name, $course, $year, $about, $photo, $uid);
    $stmt->execute();

    $conn->query("DELETE FROM skills WHERE user_id=$uid");
    if (!empty($_POST['skill_name'])) {
        foreach ($_POST['skill_name'] as $i => $skill_name) {
            $skill = trim($conn->real_escape_string($skill_name));
            $level = $conn->real_escape_string($_POST['skill_level'][$i]);
            if ($skill !== '') {
                $conn->query("INSERT INTO skills (user_id, skill_name, skill_level) VALUES ($uid, '$skill', '$level')");
            }
        }
    }

    header('Location: dashboard.php');
    exit;
}

include 'includes/header.php';
?>
<div class="container">
    <div class="form-card" style="max-width: 800px; margin: auto;">
        <h2 style="margin-bottom: 1.5rem;">Edit Profile</h2>
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom: 1rem;">
                <label for="name">Full Name</label>
                <input id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label for="course">Course</label>
                    <input id="course" name="course" value="<?php echo htmlspecialchars($user['course']); ?>">
                </div>
                <div>
                    <label for="year_level">Year Level</label>
                    <input id="year_level" name="year_level" value="<?php echo htmlspecialchars($user['year_level']); ?>">
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label for="about">About You</label>
                <textarea id="about" name="about" rows="4"><?php echo htmlspecialchars($user['about']); ?></textarea>
            </div>

            <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Manage Skills</h3>
            <div id="skills-container" style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if ($skills_res->num_rows > 0) : ?>
                    <?php while ($s = $skills_res->fetch_assoc()) : ?>
                        <div class="skill-item" style="display: flex; gap: 1rem; align-items: center;">
                            <input name="skill_name[]" placeholder="Skill Name" value="<?php echo htmlspecialchars($s['skill_name']); ?>" style="flex-grow: 1; margin: 0;">
                            <select name="skill_level[]" style="margin: 0;">
                                <option <?php if ($s['skill_level'] == 'Beginner') echo 'selected'; ?>>Beginner</option>
                                <option <?php if ($s['skill_level'] == 'Intermediate') echo 'selected'; ?>>Intermediate</option>
                                <option <?php if ($s['skill_level'] == 'Advanced') echo 'selected'; ?>>Advanced</option>
                            </select>
                            <button type="button" class="btn-small remove-skill" style="background: #f8d7da; color: #721c24;">Remove</button>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="skill-item" style="display: flex; gap: 1rem; align-items: center;">
                        <input name="skill_name[]" placeholder="Skill Name" style="flex-grow: 1; margin: 0;">
                        <select name="skill_level[]" style="margin: 0;">
                            <option>Beginner</option>
                            <option>Intermediate</option>
                            <option>Advanced</option>
                        </select>
                        <button type="button" class="btn-small remove-skill" style="background: #f8d7da; color: #721c24;">Remove</button>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="add-skill" class="btn-outline" style="margin-top: 1rem;">+ Add Skill</button>

            <div style="margin-top: 2rem; margin-bottom: 1.5rem;">
                <label for="photo">Profile Photo (optional)</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>

            <div class="form-actions">
                <a class="btn-outline" href="dashboard.php">Cancel</a>
                <button type="submit" class="btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('add-skill').onclick = () => {
    const container = document.getElementById('skills-container');
    const div = document.createElement('div');
    div.className = 'skill-item';
    div.style = 'display: flex; gap: 1rem; align-items: center;';
    div.innerHTML = `
        <input name="skill_name[]" placeholder="Skill Name" style="flex-grow: 1; margin: 0;">
        <select name="skill_level[]" style="margin: 0;">
            <option>Beginner</option>
            <option>Intermediate</option>
            <option>Advanced</option>
        </select>
        <button type="button" class="btn-small remove-skill" style="background: #f8d7da; color: #721c24;">Remove</button>`;
    container.appendChild(div);
};

document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-skill')) {
        e.target.parentElement.remove();
    }
});
</script>

<?php include 'includes/footer.php'; ?>