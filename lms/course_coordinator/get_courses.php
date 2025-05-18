<?php
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../auth.php';



$user_id = $_SESSION['user_id'];

// Fetch the coordinator's department from the course_coordinators table
$sql = "SELECT department_id FROM course_coordinators WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Coordinator not found']);
    exit();
}

$department = $result->fetch_assoc();
$department_id = $department['department_id'];

// Now fetch courses that belong to this department (since department_id is in courses table)
$sql = "SELECT course_id, course_name
        FROM courses
        WHERE department_id = '$department_id'
        ORDER BY course_name ASC";

$result = $conn->query($sql);

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
