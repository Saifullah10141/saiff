<?php
require_once '../db_connect.php';
require_once '../auth.php';

// Filter parameters from frontend
$role = $_GET['role'] ?? 'all';
$regNo = $_GET['regNo'] ?? '';
$date = $_GET['date'] ?? '';

$sql = "SELECT ua.activity_id, u.username, u.user_id, ua.role, ua.action,
               DATE(ua.activity_time) as activity_date, TIME(ua.activity_time) as activity_time, ua.ip_address
        FROM user_activities ua
        JOIN users u ON ua.user_id = u.user_id
        WHERE 1=1";

$params = [];
$types = "";

if ($role !== 'all') {
    $sql .= " AND ua.role = ?";
    $types .= "s";
    $params[] = $role;
}

if (!empty($regNo)) {
    $sql .= " AND u.user_id LIKE ?";
    $types .= "s";
    $params[] = "%" . $regNo . "%";
}

if (!empty($date)) {
    $sql .= " AND DATE(ua.activity_time) = ?";
    $types .= "s";
    $params[] = $date;
}

$sql .= " ORDER BY ua.activity_time DESC";

$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

header('Content-Type: application/json');
echo json_encode($activities);
?>
