<?php
require_once '../db_connect.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['student_id'], $_POST['assignment_id'])) {
    $student_id = $_POST['student_id'];
    $assignment_id = $_POST['assignment_id'];

    // Check if student is enrolled in the assignment
    $sql_check = "SELECT * FROM enrollments WHERE student_id = '$student_id' AND assignment_id = '$assignment_id'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows == 0) {
        echo json_encode(['error' => 'Student is not enrolled in this assignment']);
        exit();
    }

    // Delete the student from the enrollments table
    $sql_delete = "DELETE FROM enrollments WHERE student_id = '$student_id' AND assignment_id = '$assignment_id'";
    
    if ($conn->query($sql_delete)) {
        echo json_encode(['success' => 'Student deleted successfully!']);
    } else {
        echo json_encode(['error' => 'Failed to delete student']);
    }
}
?>
