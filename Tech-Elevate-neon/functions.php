<?php
// functions.php
// Shared utility functions for Tech-Elevate-Neon

function logActivity($conn, $user_id, $action, $details = null) {
    if (!$user_id || !$action) return;

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    $stmt = $conn->prepare("
        INSERT INTO activity_log (user_id, action, ip_address, user_agent)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $user_id, $action, $ip, $agent);
    $stmt->execute();
    $stmt->close();
}

// Helper: sanitize string before output
function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper: check if admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>
