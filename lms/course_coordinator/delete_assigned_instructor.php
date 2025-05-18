<?php
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignment_id = $_POST['assignment_id'];

    if (empty($assignment_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing assignment ID.']);
        exit;
    }

    $sql = "DELETE FROM assigned_subjects WHERE assignment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $assignment_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Instructor removed successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove instructor.']);
    }
}
?>
