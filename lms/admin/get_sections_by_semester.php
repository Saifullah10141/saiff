<?php
require_once "../db_connect.php";
// get_sections_by_semester.php
if (isset($_GET['semester_id'])) {
    $semesterId = $_GET['semester_id'];
    $query = "SELECT * FROM sections WHERE semester_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $semesterId);
    $stmt->execute();
    $result = $stmt->get_result();
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    echo json_encode(['sections' => $sections]);
}
?>
