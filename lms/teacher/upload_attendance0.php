<?php
require_once "../db_connect.php";
require_once "../auth.php";
$user_id = $_SESSION['user_id'];
// Step 1: Get instructor_id using the email (user_id)
$instructor_id = null;
$stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
$stmt->bind_param("s", $user_id);  // $user_id is email
$stmt->execute();
$stmt->bind_result($instructor_id);
$stmt->fetch();
$stmt->close();

$subjects = [];
if ($instructor_id) {
    // Step 2: Fetch assigned subjects for this instructor
    $subjectQuery = "SELECT assignment_id, name FROM assigned_subjects WHERE instructor_id = ?";
    $stmt = $conn->prepare($subjectQuery);
    $stmt->bind_param("s", $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();
}
?>
