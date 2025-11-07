<?php
session_start();
require '../db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$totalProjects = $conn->query("SELECT COUNT(*) as c FROM projects")->fetch_assoc()['c'];
$recentProjects = $conn->query("SELECT p.*, u.name FROM projects p JOIN users u ON u.id = p.user_id ORDER BY p.uploaded_at DESC LIMIT 5");

include '../includes/header.php';
?>
<div class="page-body">
    <div class="admin-header">
        <h1 class="page-title">Admin Dashboard</h1>
        <div class="admin-header-actions">
            <a class="btn" href="users.php">Manage Users</a>
            <a class="btn-outline" href="admin_activity_log.php">View Activity Log</a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="admin-stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Projects</h3>
            <p><?php echo $totalProjects; ?></p>
        </div>
    </div>

    <!-- Recent Projects Table -->
    <div class="admin-table-container">
        <div class="admin-table-header">
            <h2>Recent Projects</h2>
        </div>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Project Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recentProjects->num_rows > 0) : ?>
                    <?php while ($project = $recentProjects->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['project_title']); ?></td>
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td>
                                <span class="status-pill status-pill-<?php echo $project['status'] === 'active' ? 'green' : 'red'; ?>">
                                    <?php echo ucfirst($project['status']); ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <form method="post" action="toggle_project.php" style="display: inline-block;">
                                    <input type="hidden" name="pid" value="<?php echo $project['id']; ?>">
                                    <button type="submit" class="btn-outline action-btn" name="action" value="<?php echo $project['status'] === 'active' ? 'deactivate' : 'activate'; ?>">
                                        <?php echo $project['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 3rem;">No projects found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>