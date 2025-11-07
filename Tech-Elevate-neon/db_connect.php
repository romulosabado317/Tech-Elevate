<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "tech_elevate";

$conn = @new mysqli($servername, $username, $password, $database);

if ($conn->connect_errno) {
    // If connection failed, show a neon-styled alert instructing to import SQL.
    // The alert pulses and has loading dots, then fades out after 3 seconds.
    http_response_code(200);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Tech Elevate — Database Required</title>';
    // Inline minimal CSS matching neon theme
    echo '<style>\n    body{margin:0;background:linear-gradient(180deg,#05040a,#0b0710);font-family:Inter,Arial,sans-serif;color:#e6eef8;display:flex;align-items:center;justify-content:center;height:100vh}\n    .db-alert{max-width:720px;padding:24px;border-radius:12px;background:linear-gradient(180deg, rgba(168,85,247,0.06), rgba(217,70,239,0.02));box-shadow:0 12px 50px rgba(168,85,247,0.12);text-align:center;animation:pulse 3s linear;}\n    @keyframes pulse{0%{box-shadow:0 8px 30px rgba(168,85,247,0.06)}50%{box-shadow:0 18px 60px rgba(217,70,239,0.12)}100%{box-shadow:0 8px 30px rgba(168,85,247,0.06)}}\n    .db-alert h2{margin:0 0 8px 0;color:#fff}\n    .db-path{background:rgba(0,0,0,0.15);padding:8px;border-radius:6px;margin:12px 0;font-family:monospace}\n    .dots{display:inline-block;margin-left:8px}\n    .dot{display:inline-block;width:8px;height:8px;margin:0 4px;border-radius:50%;background:#d946ef;box-shadow:0 6px 18px rgba(217,70,239,0.12);animation:dot 1s infinite;}\n    .dot:nth-child(2){animation-delay:.15s}\n    .dot:nth-child(3){animation-delay:.3s}\n    @keyframes dot{50%{transform:translateY(-6px);opacity:.85}}\n    .fadeout{animation:fadeOut 1s ease 3s forwards}\n    @keyframes fadeOut{to{opacity:0;transform:translateY(-8px)}}\n    .loader-row{margin-top:10px}\n    .btn{display:inline-block;margin-top:12px;padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,#a855f7,#d946ef);color:#fff;text-decoration:none}\n    </style>';
    echo '</head><body>';
    echo '<div class="db-alert fadeout"><h2>⚠️ Database not found</h2>';
    echo '<p>Please import the <strong>tech_elevate.sql</strong> file to create the required database.</p>';
    echo '<div class="db-path">/database/tech_elevate.sql</div>';
    echo '<div class="loader-row">Importing required? Wait... <span class="dots"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span></div>';
    echo '<a class="btn" href="README.txt">Open README</a>';
    echo '</div>';
    // small JS to remove the alert after 4s to ensure fade completes and stop further rendering
    echo '<script>setTimeout(()=>{document.querySelector(".db-alert")?.remove();},4000);</script>';
    echo '</body></html>';
    exit;
}

// Set timezone for PHP and MySQL to ensure consistency
date_default_timezone_set('Asia/Manila');
$conn->query("SET time_zone = '+08:00'");
?>