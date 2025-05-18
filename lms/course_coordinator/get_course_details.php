<?php
require_once "../db_connect.php";

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Query to get course details
    $sql = "SELECT course_name, course_id, credit_hours_theory, credit_hours_practical FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_id);
    $stmt->execute();
    $stmt->bind_result($course_name, $course_code, $credit_hours_theory, $credit_hours_practical);
    $stmt->fetch();
    $stmt->close();

    // Prepare the data in an array
    $course_details = array(
        "course_id" => $course_id,
        "course_name" => $course_name,
        "course_code" => $course_code,
        "credit_hours_theory" => $credit_hours_theory,
        "credit_hours_practical" => $credit_hours_practical
    );

    // Return the data as JSON
    echo json_encode($course_details);
} else {
    echo json_encode(array("error" => "No course ID provided"));
}
