<?php
require_once '../auth.php';
require_once '../db_connect.php';

$user_id = $_SESSION['user_id'];

// SQL query
$sql = "SELECT u.username, u.user_id, i.instructor_id, i.father_name, i.cnic, i.dob, i.gender,
               f.name AS faculty_name, d.name AS department_name, i.rank
        FROM users u
        JOIN instructors i ON u.user_id = i.user_id
        LEFT JOIN faculties f ON i.faculty_id = f.faculty_id
        LEFT JOIN departments d ON i.department_id = d.department_id
        WHERE u.user_id = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $instructor = $result->fetch_assoc();
} else {
    echo "Instructor not found.";
    exit();
}

// Sanitize output
$username = htmlspecialchars($instructor['username']);
$instructor_id = htmlspecialchars($instructor['instructor_id']);
$father_name = htmlspecialchars($instructor['father_name']);
$cnic = htmlspecialchars($instructor['cnic']);
$dob = htmlspecialchars($instructor['dob']);
$gender = htmlspecialchars($instructor['gender']);
$faculty_name = htmlspecialchars($instructor['faculty_name']);
$department_name = htmlspecialchars($instructor['department_name']);
$rank = htmlspecialchars($instructor['rank']);
?>
