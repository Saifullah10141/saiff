<?php
require '../db_connect.php';
require '../auth.php';

$stmt = $conn->prepare("SELECT department_id FROM lms_managers WHERE user_id = ?");
        $stmt->bind_param("s", $_SESSION['user_id']); // assuming email is used as ID
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $department_id = $row['department_id'];
        }

$result = $conn->query("SELECT * FROM degrees WHERE department_id = '$department_id'");
$degrees = [];
while ($row = $result->fetch_assoc()) {
    $degrees[] = $row;
}
echo json_encode($degrees);
?>
