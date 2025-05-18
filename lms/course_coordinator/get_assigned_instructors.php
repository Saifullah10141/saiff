<?php
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../auth.php';



$user_id = $_SESSION['user_id'];

// Fetch the coordinator's department from the users table
$sql = "SELECT department_id FROM course_coordinators WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Coordinator not found']);
    exit();
}

$department = $result->fetch_assoc();
$department_id = $department['department_id'];

// Now fetch assigned subjects filtered by coordinator's department
$sql = "SELECT 
            a.assignment_id, 
            u.username AS instructor_name, 
            i.instructor_id, 
            a.course_id, 
            a.name AS course_name, 
            d.name AS department_name
        FROM assigned_subjects a
        JOIN instructors i ON a.instructor_id = i.instructor_id
        JOIN users u ON i.user_id = u.user_id
        JOIN departments d ON i.department_id = d.department_id
        JOIN courses c ON a.course_id = c.course_id
        WHERE c.department_id = '$department_id'";

$result = $conn->query($sql);

$assignedSubjects = [];
while ($row = $result->fetch_assoc()) {
    $assignedSubjects[] = $row;
}

echo json_encode($assignedSubjects);
?>
