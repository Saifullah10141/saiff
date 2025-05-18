<?php
require_once '../db_connect.php';
require_once '../auth.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $activityId = intval($data['id']);

    $stmt = $conn->prepare("DELETE FROM user_activities WHERE activity_id = ?");
    $stmt->bind_param("i", $activityId);

    if ($stmt->execute()) {
        echo "Activity deleted successfully.";
    } else {
        echo "Error deleting activity.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
