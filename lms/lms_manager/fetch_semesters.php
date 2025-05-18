<?php
require_once '../db_connect.php';
if (isset($_POST['degree_id'])) {
    $degree_id = $_POST['degree_id'];
    $stmt = $conn->prepare("SELECT semester_id, name FROM semesters WHERE degree_id = ?");
    $stmt->bind_param("i", $degree_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo '<option value="">Select Semester</option>';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['semester_id']}'>{$row['name']}</option>";
    }
}
?>