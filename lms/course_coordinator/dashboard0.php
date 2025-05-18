<?php
require_once "../db_connect.php"; // database connection
require_once "../auth.php";        // session and login check

// Get logged-in user's email from session
$user_id = $_SESSION['user_id'];

// Fetch course coordinator info based on email
$sql = "SELECT 
            u.username AS name,                 
            cc.father_name,
            cc.coordinator_id AS registration_no,
            cc.cnic,
            u.user_id AS email,
            cc.dob,
            cc.gender,
            f.name AS faculty_name,      
            d.name AS department_name 
        FROM users u
        INNER JOIN course_coordinators cc ON u.user_id = cc.user_id
        LEFT JOIN faculties f ON cc.faculty_id = f.faculty_id
        LEFT JOIN departments d ON cc.department_id = d.department_id
        WHERE u.user_id = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $coordinator = $result->fetch_assoc();
} else {
    die("Coordinator info not found.");
}
?>
