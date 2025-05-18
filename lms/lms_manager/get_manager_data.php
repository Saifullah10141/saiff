<?php
require '../db_connect.php';
require '../auth.php';

$user_email = $_SESSION['user_id']; // Email from session

// Step 1: Get manager_id (reg. no) from users table
$getManagerIdQuery = "SELECT manager_id FROM lms_managers WHERE user_id = ?";
$stmt = $conn->prepare($getManagerIdQuery);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Manager not found.");
}

$row = $result->fetch_assoc();
$manager_id = $row['manager_id']; // Now you have the registration number

// Step 2: Get faculty_id and department_id from lms_managers using manager_id
$query = "
    SELECT father_name, cnic, dob, gender, faculty_id, department_id
    FROM lms_managers
    WHERE manager_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $manager_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $father_name = $row['father_name']; 
    $cnic = $row['cnic']; 
    $dob = $row['dob']; 
    $gender = $row['gender']; 
    $faculty_id = $row['faculty_id'];
    $department_id = $row['department_id'];
} else {
    die("Faculty or department not found.");
}


?>
