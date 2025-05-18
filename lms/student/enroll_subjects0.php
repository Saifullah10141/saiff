<?php
require_once '../db_connect.php';
require_once '../auth.php';


$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

$sql = "SELECT student_id FROM students WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    $student = $result->fetch_assoc();
    $student_id = $student['student_id'];

// Fetch the student's enrolled subjects based on the enrollment table
$sql = "SELECT es.assignment_id, asg.name AS course_name
        FROM enrollments es
        INNER JOIN assigned_subjects asg ON es.assignment_id = asg.assignment_id
        WHERE es.student_id = '$student_id'";

$result = $conn->query($sql);

// Check if subjects are found
if ($result->num_rows > 0) {
    $subjects = $result->fetch_all(MYSQLI_ASSOC);  // Store subjects in an array
} else {
    $subjects = [];  // No subjects enrolled
}

?>
