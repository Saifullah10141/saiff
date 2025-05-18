<?php
header('Content-Type: application/json');
require '../db_connect.php';
require '../auth.php';

// Optional: suppress warnings that mess up JSON
ini_set('display_errors', 0);
error_reporting(0);

$query = "SELECT cc.*, u.username, f.name AS faculty_name, d.name AS department_name
        FROM course_coordinators cc
        JOIN faculties f ON cc.faculty_id = f.faculty_id
        JOIN departments d ON cc.department_id = d.department_id
        JOIN users u ON cc.user_id = u.user_id";

$result = mysqli_query($conn, $query);

$coordinators = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $coordinators[] = $row;
    }
    echo json_encode($coordinators);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
