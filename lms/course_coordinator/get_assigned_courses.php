<?php
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../auth.php';

$user_id = $_SESSION['user_id'];

// Fetch the coordinator's department
$sql = "SELECT department_id FROM course_coordinators WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Coordinator not found']);
    exit();
}

$department = $result->fetch_assoc();
$department_id = $department['department_id'];

// Now fetch assigned subjects (assignment_id + course info) that belong to this department
$sql = "SELECT 
            a.assignment_id,
            a.name,
            c.course_id, 
            c.course_name
        FROM assigned_subjects a
        JOIN courses c ON a.course_id = c.course_id
        WHERE c.department_id = '$department_id'
        ORDER BY c.course_name ASC";

$result = $conn->query($sql);

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row; // now each row has assignment_id, course_id, course_name
}

echo json_encode(['courses' => $courses]);
?>
