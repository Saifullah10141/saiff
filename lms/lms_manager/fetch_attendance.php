<?php
include '../db_connect.php';
include '../auth.php';

$degree_id = $_POST['degree_id'];
$semester_id = $_POST['semester_id'];
$section_id = $_POST['section_id'];

$sql = "SELECT u.username, s.student_id, d.name AS degree, sem.name AS semester, sec.name AS section, ar.status, a.session_date, a.session_time
        FROM attendance_records ar
        JOIN attendances a ON ar.attendance_id = a.attendance_id
        JOIN students s ON ar.student_id = s.student_id
        JOIN users u ON s.user_id = u.user_id
        JOIN degrees d ON s.degree_id = d.degree_id
        JOIN semesters sem ON s.semester_id = sem.semester_id
        JOIN sections sec ON s.section_id = sec.section_id
        WHERE 1=1";
// Add conditions based on selected filters
if (!empty($degree_id)) {
    $sql .= " AND s.degree_id = ?";
}
if (!empty($semester_id)) {
    $sql .= " AND s.semester_id = ?";
}
if (!empty($section_id)) {
    $sql .= " AND s.section_id = ?";
}

// Prepare statement
$stmt = $conn->prepare($sql);
$bind_types = "";
$bind_params = [];

// Bind parameters dynamically based on which filters are used
if (!empty($degree_id)) {
    $bind_types .= "i";
    $bind_params[] = $degree_id;
}
if (!empty($semester_id)) {
    $bind_types .= "i";
    $bind_params[] = $semester_id;
}
if (!empty($section_id)) {
    $bind_types .= "i";
    $bind_params[] = $section_id;
}

if (!empty($bind_types)) {
    $stmt->bind_param($bind_types, ...$bind_params);
}

$stmt->execute();
$result = $stmt->get_result();
$data = [];
$counts = ['Present' => 0, 'Absent' => 0, 'Leave' => 0];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    $counts[$row['status']]++;
}

echo json_encode([
    "records" => $data,
    "counts" => $counts
]);
?>