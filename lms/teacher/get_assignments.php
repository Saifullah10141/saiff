<?php
require_once "../auth.php";
require_once "../db_connect.php";

$subject_id = $_GET['subject_id'];

$query = "SELECT assignment_id, title, due_date FROM assignments WHERE assignment_id1 = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$assignments = [];
while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}
echo json_encode($assignments);
?>
