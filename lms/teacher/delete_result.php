<?php
require_once "../db_connect.php";
require_once "../auth.php";

if (isset($_POST['result_id'])) {
    $stmt = $conn->prepare("DELETE FROM results WHERE result_id = ?");
    $stmt->bind_param("i", $_POST['result_id']);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
