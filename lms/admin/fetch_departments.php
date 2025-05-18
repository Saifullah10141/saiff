<?php
require_once '../db_connect.php';

if (isset($_POST['faculty'])) {
    $faculty = $_POST['faculty'];
    
    // Fetch faculty_id
    $faculty_stmt = $conn->prepare("SELECT faculty_id FROM faculties WHERE name = ?");
    $faculty_stmt->bind_param("s", $faculty);
    $faculty_stmt->execute();
    $faculty_result = $faculty_stmt->get_result();
    $faculty_data = $faculty_result->fetch_assoc();
    $faculty_id = $faculty_data['faculty_id'];

    // Fetch departments
    $dept_stmt = $conn->prepare("SELECT name FROM departments WHERE faculty_id = ?");
    $dept_stmt->bind_param("i", $faculty_id);
    $dept_stmt->execute();
    $departments = $dept_stmt->get_result();

    echo '<option value="">Select Department</option>';
    while ($dept = $departments->fetch_assoc()) {
        echo "<option value='{$dept['name']}'>{$dept['name']}</option>";
    }
}
?>
