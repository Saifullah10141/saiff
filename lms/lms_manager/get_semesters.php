<?php
require '../db_connect.php';
$degree_id = $_GET['degree_id'];
$stmt = $conn->prepare("SELECT * FROM semesters WHERE degree_id = ?");
$stmt->bind_param("i", $degree_id);
$stmt->execute();
$result = $stmt->get_result();

$semesters = [];
while ($row = $result->fetch_assoc()) {
    $semesters[] = $row;
}
echo json_encode($semesters);
?>
