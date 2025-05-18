<?php
require_once '../auth.php';
require_once '../db_connect.php';

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Prepare the SQL query to fetch student data
$sql = "SELECT u.username, u.user_id, s.student_id, s.father_name, s.cnic, s.dob, s.gender,
               f.name AS faculty, d.name AS department, deg.name AS degree,
               sem.name AS semester, sec.name AS section
        FROM users u
        JOIN students s ON u.user_id = s.user_id
        LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
        LEFT JOIN departments d ON s.department_id = d.department_id
        LEFT JOIN degrees deg ON s.degree_id = deg.degree_id
        LEFT JOIN semesters sem ON s.semester_id = sem.semester_id
        LEFT JOIN sections sec ON s.section_id = sec.section_id
        WHERE u.user_id = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);  // Bind user_id parameter
$stmt->execute();
$result = $stmt->get_result();

// Check if the student was found
if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
} else {
    echo "Student not found.";
    exit();
}

// Sanitize output to prevent XSS attacks when displaying student information
$username = htmlspecialchars($student['username']);
$student_id = htmlspecialchars($student['student_id']);
$father_name = htmlspecialchars($student['father_name']);
$cnic = htmlspecialchars($student['cnic']);
$dob = htmlspecialchars($student['dob']);
$gender = htmlspecialchars($student['gender']);
$faculty = htmlspecialchars($student['faculty']);
$department = htmlspecialchars($student['department']);
$degree = htmlspecialchars($student['degree']);
$semester = htmlspecialchars($student['semester']);
$section = htmlspecialchars($student['section']);
?>