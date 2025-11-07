<?php
session_start();
require '../db_connect.php';
require '../functions.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header('Location: ../login.php');
    exit;
}

$pid = (int)($_POST['pid'] ?? 0);
$action = $_POST['action'] ?? '';

if ($pid <= 0 || !in_array($action, ['deactivate','activate'])) {
    header('Location: dashboard.php');
    exit;
}

// --- Update project status ---
$status = ($action === 'deactivate') ? 'inactive' : 'active';
$conn->query("UPDATE projects SET status='$status' WHERE id=$pid");

// --- Fetch project info for logging ---
$project = $conn->query("
    SELECT p.project_title, p.user_id, u.name AS student_name
    FROM projects p
    JOIN users u ON u.id = p.user_id
    WHERE p.id = $pid
")->fetch_assoc();

// --- Log admin action ---
$admin_id = $_SESSION['user_id'];
$project_title = $project['project_title'];
$student_name = $project['student_name'];
$student_id = $project['user_id'];

logActivity($conn, $admin_id, "Admin {$action}d project", "$project_title by $student_name");

// --- Add to admin_reviews table ---
$stmt = $conn->prepare("
    INSERT INTO admin_reviews (admin_id, reviewed_user, action_taken, remarks)
    VALUES (?, ?, ?, ?)
");
$remarks = "Project '{$project_title}' was {$status}d by admin.";
$stmt->bind_param("iiss", $admin_id, $student_id, $action, $remarks);
$stmt->execute();
$stmt->close();

header('Location: dashboard.php');
exit;
?>
