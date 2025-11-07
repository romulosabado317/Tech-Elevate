<?php
session_start();
require '../db_connect.php';

// Ensure user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get user ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$user_id_to_delete = (int)$_GET['id'];
$admin_id = (int)$_SESSION['user_id'];

// Prevent admin from deleting their own account
if ($user_id_to_delete === $admin_id) {
    header('Location: users.php?error=Cannot delete your own account');
    exit;
}

// Delete user from database
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id_to_delete);

if ($stmt->execute()) {
    header('Location: users.php?success=User deleted');
} else {
    header('Location: users.php?error=Failed to delete user');
}

$stmt->close();
exit;
?>