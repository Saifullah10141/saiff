<?php
require_once "../db_connect.php";
require_once "../auth.php";

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

// Query to fetch department_id of the logged-in course coordinator
$department_sql = "SELECT department_id FROM course_coordinators WHERE user_id = ?";
$department_stmt = $conn->prepare($department_sql);
$department_stmt->bind_param("s", $user_id);
$department_stmt->execute();
$department_stmt->bind_result($department_id);
$department_stmt->fetch();
$department_stmt->close();

// Fetch by department_id
$sql = "SELECT c.course_name, c.course_id, c.credit_hours_theory, c.credit_hours_practical
        FROM courses c WHERE c.department_id = ?"; // Filter by department_id
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $department_id); // Pass department_id to query
$stmt->execute();
$result = $stmt->get_result();

// Store courses in an array
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}


$stmt->close();
$conn->close();
?>