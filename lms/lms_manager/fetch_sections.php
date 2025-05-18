<?php
require_once '../db_connect.php';
if (isset($_POST['semester_id'])) {
    $semester_id = $_POST['semester_id'];
    $stmt = $conn->prepare("SELECT section_id, name FROM sections WHERE semester_id = ?");
    $stmt->bind_param("i", $semester_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo '<option value="">Select Section</option>';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['section_id']}'>{$row['name']}</option>";
    }
}
?>