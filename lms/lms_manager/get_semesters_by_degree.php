<?php
require_once "../db_connect.php";
// get_semesters_by_degree.php
if (isset($_GET['degree_id'])) {
    $degreeId = $_GET['degree_id'];
    $query = "SELECT * FROM semesters WHERE degree_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $degreeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $semesters = [];
    while ($row = $result->fetch_assoc()) {
        $semesters[] = $row;
    }
    echo json_encode(['semesters' => $semesters]);
}
?>
