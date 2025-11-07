<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$user = $conn->query("SELECT id,name,email,photo,course,year_level,about FROM users WHERE id=$uid")->fetch_assoc();
$projects = $conn->query("SELECT * FROM projects WHERE user_id=$uid ORDER BY uploaded_at DESC");
$skills = $conn->query("SELECT * FROM skills WHERE user_id=$uid ORDER BY created_at DESC");

include 'includes/header.php';
?>
 <div class="page-body dashboard-grid">

    <!-- Left Sidebar: Profile -->
    <aside>
        <div class="card text-center">
            <div class="profile-photo" style="margin-bottom: 1.5rem;">
                <?php if ($user['photo'] && file_exists('uploads/profile_photos/' . $user['photo'])) : ?>
                    <img src="uploads/profile_photos/<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile photo" style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid var(--primary-color); object-fit: cover;">
                <?php else : ?>
                    <img src="assets/img/default.png" alt="Default Profile Photo" style="width: 120px; height: 120px; border-radius: 50%;">
                <?php endif; ?>
            </div>
            <h2 style="font-weight: 700;"><?php echo htmlspecialchars($user['name']); ?></h2>
            <p class="muted"><?php echo htmlspecialchars($user['course'] . ' · ' . $user['year_level']); ?></p>
            <p style="margin: 1.5rem 0;"><?php echo nl2br(htmlspecialchars($user['about'])); ?></p>
            <a class="btn w-100" href="edit_profile.php">Edit Profile</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main>
        <!-- Projects Section -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="font-weight: 700;">My Projects</h3>
                <div style="display: flex; gap: 1rem;">
                    <a class="btn-outline" href="upload_project.php">Upload Project</a>
                    <a class="btn" href="explore.php">Explore</a>
                </div>
            </div>

            <?php if ($projects->num_rows === 0) : ?>
                <div class="text-center muted" style="padding: 3rem 0;">
                    <p>You haven't uploaded any projects yet.</p>
                    <p>Click 'Upload Project' to get started.</p>
                </div>
            <?php else : ?>
                <div class="projects-list">
                    <?php while ($p = $projects->fetch_assoc()) : ?>
                        <div class="proj-item">
                            <div class="file-icon">
                                <?php if ($p['filename'] && preg_match('/\.(jpg|jpeg|png|gif)$/i', $p['filename'])) : ?>
                                    <img src="uploads/<?php echo htmlspecialchars($p['filename']); ?>" alt="Project thumbnail">
                                <?php else : ?>
                                    <span>DOC</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex-grow: 1;">
                                <strong style="font-weight: 600;"><?php echo htmlspecialchars($p['project_title']); ?></strong>
                                <p class="muted" style="font-size: 0.9rem;">Uploaded on: <?php echo date('M d, Y', strtotime($p['uploaded_at'])); ?></p>
                            </div>
                            <a href="project_view.php?id=<?php echo $p['id']; ?>" class="btn-outline">View</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Skills Section -->
        <div class="card mt-2">
            <h3 style="font-weight: 700; margin-bottom: 2rem;">My Skills</h3>
            <form method="POST" action="add_skill.php" style="display: flex; gap: 1rem; margin-bottom: 2rem; align-items: flex-end;">
                <div style="flex-grow: 1;" class="form-group">
                    <label for="skill_name">Skill Name</label>
                    <input type="text" id="skill_name" name="skill_name" placeholder="e.g., PHP, Python" required>
                </div>
                <div class="form-group">
                    <label for="skill_level">Level</label>
                    <select id="skill_level" name="skill_level">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                </div>
                <button class="btn" type="submit">Add</button>
            </form>

            <div class="skills-list">
                <?php if ($skills->num_rows > 0) : ?>
                    <?php while ($s = $skills->fetch_assoc()) : ?>
                        <div class="skill-item" style="justify-content: space-between;">
                            <div>
                                <span style="font-weight: 600;"><?php echo htmlspecialchars($s['skill_name']); ?></span>
                                <span class="muted">(<?php echo htmlspecialchars($s['skill_level']); ?>)</span>
                            </div>
                            <a href="skills_delete.php?id=<?php echo $s['id']; ?>" class="btn-danger">Remove</a>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="text-center muted" style="padding: 2rem 0;">
                        <p>No skills added yet. Use the form above to add your skills.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

</div>

<?php include 'includes/footer.php'; ?>