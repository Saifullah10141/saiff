<?php
require_once "../auth.php";
require_once "../db_connect.php";

$user_id = $_SESSION['user_id']; // This is the email

// Get instructor_id from instructors table
$query = "SELECT instructor_id FROM instructors WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($instructor_id);
$stmt->fetch();
$stmt->close();

// Now use instructor_id to get subjects
$query = "SELECT assignment_id, name FROM assigned_subjects WHERE instructor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode($subjects);
?>
