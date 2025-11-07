<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int)$_SESSION['user_id'];
    $skill_name = trim($_POST['skill_name']);
    $skill_level = $_POST['skill_level'];

    if ($skill_name !== '') {
        $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, skill_level) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $uid, $skill_name, $skill_level);
        $stmt->execute();
        $stmt->close();
    }
}
header("Location: dashboard.php");
exit;
?>
