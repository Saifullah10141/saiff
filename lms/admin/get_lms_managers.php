<?php
header('Content-Type: application/json');
require '../db_connect.php';
require '../auth.php';

// Optional: suppress warnings that mess up JSON
ini_set('display_errors', 0);
error_reporting(0);

$query = "SELECT ll.*, u.username, f.name AS faculty_name, d.name AS department_name
        FROM lms_managers ll
        JOIN faculties f ON ll.faculty_id = f.faculty_id
        JOIN departments d ON ll.department_id = d.department_id
        JOIN users u ON ll.user_id = u.user_id";

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
