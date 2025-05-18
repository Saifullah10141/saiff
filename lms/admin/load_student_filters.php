<?php
require_once '../db_connect.php';

$action = $_GET['action'] ?? '';

if ($action === 'faculties') {
    $query = "SELECT DISTINCT faculty_name FROM faculties ORDER BY faculty_name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculties = [];
    while ($row = $result->fetch_assoc()) {
        $faculties[] = $row['faculty_name'];
    }
    echo json_encode($faculties);
}

elseif ($action === 'departments' && isset($_GET['faculty'])) {
    $faculty = $_GET['faculty'];
    $query = "SELECT DISTINCT department_name FROM departments WHERE faculty_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $faculty);
    $stmt->execute();
    $result = $stmt->get_result();
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['department_name'];
    }
    echo json_encode($departments);
}

elseif ($action === 'degrees' && isset($_GET['department'])) {
    $department = $_GET['department'];
    $query = "SELECT DISTINCT degree_name FROM degrees WHERE department_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $department);
    $stmt->execute();
    $result = $stmt->get_result();
    $degrees = [];
    while ($row = $result->fetch_assoc()) {
        $degrees[] = $row['degree_name'];
    }
    echo json_encode($degrees);
}

elseif ($action === 'semesters') {
    $query = "SELECT DISTINCT semester_name FROM semesters ORDER BY semester_name";
    $result = $conn->query($query);
    $semesters = [];
    while ($row = $result->fetch_assoc()) {
        $semesters[] = $row['semester_name'];
    }
    echo json_encode($semesters);
}

elseif ($action === 'sections') {
    $query = "SELECT DISTINCT section_name FROM sections ORDER BY section_name";
    $result = $conn->query($query);
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row['section_name'];
    }
    echo json_encode($sections);
}

else {
    echo json_encode(['error' => 'Invalid request']);
}
