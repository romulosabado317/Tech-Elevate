<?php
session_start();
require '../db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$activityLog = $conn->query("SELECT a.*, u.name FROM activity_log a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.date_time DESC");

include '../includes/header.php';
?>
<div class="page-body">
    <div class="admin-table-container">
        <div class="admin-table-header">
            <h1 class="page-title">Admin Activity Log</h1>
            <a class="btn-outline" href="dashboard.php">Back to Dashboard</a>
        </div>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $activityLog->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['name'] ?? 'Guest'); ?></td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($log['date_time']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>