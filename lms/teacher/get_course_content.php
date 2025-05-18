<?php
require_once "../auth.php";
require_once "../db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    $query = "SELECT week_no, description FROM course_contents WHERE assignment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $contents = [];
    while ($row = $result->fetch_assoc()) {
        $contents[$row['week_no']] = $row['description'];
    }

    echo json_encode($contents);
}
?>
