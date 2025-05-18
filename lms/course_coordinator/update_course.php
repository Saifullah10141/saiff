<?php
require_once "../db_connect.php"; // Database connection

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the course data from the form
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $credit_hours_theory = $_POST['credit_hours_theory'];
    $credit_hours_practical = $_POST['credit_hours_practical'];

    // Prepare the SQL update statement
    $sql = "UPDATE courses SET course_name = ?, course_id = ?, credit_hours_theory = ?, credit_hours_practical = ? WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiis", $course_name, $course_code, $credit_hours_theory, $credit_hours_practical, $course_id);

    // Execute the query
    if ($stmt->execute()) {
        // Return a success response
        echo json_encode(['success' => true]);
    } else {
        // Return an error response
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
}
?>
