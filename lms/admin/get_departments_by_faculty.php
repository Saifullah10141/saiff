<?php
require_once "../db_connect.php";
// get_departments_by_faculty.php
if (isset($_GET['faculty_id'])) {
    $facultyId = $_GET['faculty_id'];
    $query = "SELECT * FROM departments WHERE faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $facultyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    echo json_encode(['departments' => $departments]);
}
?>
