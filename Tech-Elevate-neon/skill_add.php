<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int)$_SESSION['user_id'];
    $skill = trim($_POST['skill']);
    $level = trim($_POST['level']);

    if ($skill !== '' && $level !== '') {
        $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, level) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $uid, $skill, $level);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: dashboard.php");
    exit;
}
?>
