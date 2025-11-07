<?php
session_start();
require 'db_connect.php';   // your mysqli $conn
require 'functions.php';

// redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// sanitize session values
$uid = (int) $_SESSION['user_id'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'student';

// fetch current user name safely
$stmtUser = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmtUser->bind_param("i", $uid);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
$currentUser = $resUser->fetch_assoc();
$stmtUser->close();

$currentUserName = $currentUser ? $currentUser['name'] : 'Unknown';

// Pagination (optional small safety)
$limit = 100;
$offset = 0;
if (isset($_GET['p']) && is_numeric($_GET['p'])) {
    $page = max(1, (int)$_GET['p']);
    $offset = ($page - 1) * $limit;
}

// main activity query (join user name). newest first
// note: column names match SQL provided below (timestamp, details)
$sql = "SELECT a.id, a.user_id, a.action, a.details, a.ip_address, a.user_agent, a.timestamp, u.name
        FROM activity_log a
        LEFT JOIN users u ON u.id = a.user_id
        ORDER BY a.timestamp DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$logs = $stmt->get_result();
?>
<?php include 'includes/header.php'; ?>

<div class="container">
  <h2>Activity Log</h2>

  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <div>
      <label style="font-size:0.95rem; color:#cbd5e1;">
        <input type="checkbox" id="autoRefreshToggle"> Auto-refresh
      </label>
      <small style="color:#9ca3af; margin-left:10px;">Showing latest <?php echo htmlspecialchars($limit); ?> entries</small>
    </div>

    <?php if ($role === 'admin'): ?>
    <form id="clearLogForm" method="post" action="clear_log.php" onsubmit="return confirm('Clear entire activity log? This action is irreversible.');">
      <button class="btn-outline" type="submit" name="clear_log">Clear Log (admin)</button>
    </form>
    <?php endif; ?>
  </div>

  <table class="log-table" id="logTable">
    <thead>
      <tr>
        <th>User</th>
        <th>Action</th>
        <th>Details</th>
        <th>IP</th>
        <th>User Agent</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($logs && $logs->num_rows): ?>
        <?php while($log = $logs->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($log['name'] ?? 'System'); ?></td>
            <td><?php echo htmlspecialchars($log['action']); ?></td>
            <td style="max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($log['details']); ?>">
              <?php echo htmlspecialchars($log['details']); ?>
            </td>
            <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
            <td style="max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($log['user_agent']); ?>">
              <?php echo htmlspecialchars($log['user_agent']); ?>
            </td>
            <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; color:#9ca3af;">No activity found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <p style="text-align:center; margin-top:18px;">
    <a class="btn-outline" href="dashboard.php">Back to Dashboard</a>
  </p>
</div>

<script>
// Auto-refresh implementation (polling)
let autoRefresh = false;
let refreshInterval = null;
document.getElementById('autoRefreshToggle').addEventListener('change', function(e){
  autoRefresh = e.target.checked;
  if (autoRefresh) startAutoRefresh();
  else stopAutoRefresh();
});

function startAutoRefresh(){
  if (refreshInterval) clearInterval(refreshInterval);
  refreshInterval = setInterval(fetchLatestLogs, 5000); // every 5s
  fetchLatestLogs();
}
function stopAutoRefresh(){
  if (refreshInterval) clearInterval(refreshInterval);
  refreshInterval = null;
}

function fetchLatestLogs(){
  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'activity_log_ajax.php', true);
  xhr.onload = function(){
    if (xhr.status === 200){
      const tbody = document.querySelector('#logTable tbody');
      tbody.innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

// optional: start if you want default on:
// document.getElementById('autoRefreshToggle').checked = true;
// startAutoRefresh();
</script>

<?php
$stmt->close();
include 'includes/footer.php';
?>
