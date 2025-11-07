<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$uid = (int)$_SESSION['user_id'];
$id = (int)$_GET['id'];

$conn->query("DELETE FROM skills WHERE id=$id AND user_id=$uid");
header('Location: skills_list.php');
exit;
