<?php
require_once '../db_connect.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course = $_POST['course'];
    $course_name = $_POST['course_name'];
    $instructor_id = $_POST['instructor_id'];

    $user_id = $_SESSION['user_id'];  // Assuming the user is logged in and their ID is stored in session

    // Fetch the coordinator's ID and department from the course_coordinators table
    $sql = "SELECT coordinator_id FROM course_coordinators WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    $coordinator = $result->fetch_assoc();
    $coordinator_id = $coordinator['coordinator_id'];

    if (empty($course) || empty($course_name) || empty($instructor_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all fields.']);
        exit;
    }

    // Insert into assigned_subjects table
    $sql = "INSERT INTO assigned_subjects (course_id, name, instructor_id, coordinator_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $course, $course_name, $instructor_id, $coordinator_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Instructor assigned successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to assign instructor.']);
    }
}
?>
