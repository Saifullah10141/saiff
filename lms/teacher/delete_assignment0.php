<?php
require_once "../auth.php";
require_once "../db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);
$assignment_id = $data['assignment_id'];

$query = "DELETE FROM assignments WHERE assignment_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $assignment_id);
$success = $stmt->execute();
$stmt->close();

echo json_encode(["success" => $success]);
?>
