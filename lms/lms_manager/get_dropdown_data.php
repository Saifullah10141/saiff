<?php
include '../db_connect.php';

function fetchOptions($table, $idField, $labelField) {
    global $conn;
    $data = [];
    $res = mysqli_query($conn, "SELECT $idField AS id, $labelField AS name FROM $table");
    if (!$res) {
        die("Query error on table $table: " . mysqli_error($conn));
    }
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    return $data;
}

echo json_encode([
    'faculties' => fetchOptions('faculties', 'faculty_id', 'name'),
    'departments' => fetchOptions('departments', 'department_id', 'name'),
    'degrees' => fetchOptions('degrees', 'degree_id', 'name'),
    'semesters' => fetchOptions('semesters', 'semester_id', 'name'),
    'sections' => fetchOptions('sections', 'section_id', 'name'),  // Change this line
]);
