<?php
header('Content-Type: application/json');

require_once '../auth.php';
require_once '../db_connect.php';

$degree = $_POST['degree_id'] ?? '';
$semester = $_POST['semester_id'] ?? '';
$section = $_POST['section_id'] ?? '';

$query = "
    SELECT 
        u.username AS student_name, 
        s.student_id AS registration_number, 
        d.name AS degree_name, 
        se.name AS semester_name, 
        sec.name AS section_name, 
        a.title AS assignment_title,
        sa.status
    FROM assignment_submissions sa
    JOIN students s ON sa.student_id = s.student_id
    JOIN users u ON s.user_id = u.user_id
    JOIN degrees d ON s.degree_id = d.degree_id
    JOIN semesters se ON s.semester_id = se.semester_id
    JOIN sections sec ON s.section_id = sec.section_id
    JOIN assignments a ON sa.assignment_id = a.assignment_id
    WHERE 1=1
";

if ($degree) $query .= " AND s.degree_id = '$degree'";
if ($semester) $query .= " AND s.semester_id = '$semester'";
if ($section) $query .= " AND s.section_id = '$section'";

$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'name' => $row['student_name'],
        'reg' => $row['registration_number'],
        'degree' => $row['degree_name'],
        'semester' => $row['semester_name'],
        'section' => $row['section_name'],
        'title' => $row['assignment_title'],
        'status' => $row['status']
    ];
}

echo json_encode($data);
?>
