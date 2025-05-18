<?php
require '../db_connect.php';
require '../auth.php';

$user_id = $_SESSION['user_id']; // this is the email

// Correct SQL with debug
$query = "
    SELECT 
        u.username,
        u.user_id,
        m.manager_id,
        m.father_name,
        m.cnic,
        m.dob,
        m.gender,
        f.name AS faculty_name,
        d.name AS department_name
    FROM lms_managers m
    JOIN users u ON u.user_id = m.user_id
    LEFT JOIN faculties f ON m.faculty_id = f.faculty_id
    LEFT JOIN departments d ON m.department_id = d.department_id
    WHERE u.user_id = ?
";


$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error); // Show exact error
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $manager = $result->fetch_assoc();
} else {
    die("Manager profile not found.");
}
?>
