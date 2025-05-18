<?php
header('Content-Type: application/json');
require '../db_connect.php';
require '../auth.php';
require_once '../ip.php';


function logActivity($conn, $user_id, $role, $action) {
    $ip = getUserIp();
    $stmt = $conn->prepare("INSERT INTO user_activities (user_id, role, action, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user_id, $role, $action, $ip);
    $stmt->execute();
}


$action = $_POST['action'] ?? '';

switch ($action) {
    case 'addDegree':
        $stmt = $conn->prepare("SELECT department_id FROM lms_managers WHERE user_id = ?");
        $stmt->bind_param("s", $_SESSION['user_id']); // assuming email is used as ID
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $_SESSION['department_id'] = $row['department_id'];
        }
        $degreeName = trim($_POST['degreeName']);
        if ($degreeName === '') {
            echo json_encode(['status' => 'error', 'message' => 'Degree name cannot be empty.']);
            exit;
        }
    
        $stmt = $conn->prepare("INSERT INTO degrees (name, department_id) VALUES (?, ?)");
        $stmt->bind_param("si", $degreeName, $_SESSION['department_id']);
        if ($stmt->execute()) {
            logActivity($conn, $_SESSION['user_id'], $_SESSION['role'], "Added Degree: $degreeName");
            echo json_encode(['status' => 'success', 'message' => 'Degree added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add degree.']);
        }
        break;
    
    case 'addSemester':
        $degreeId = $_POST['degreeId'];
        $semesterName = trim($_POST['semesterName']);
        if ($semesterName === '' || !$degreeId) {
            echo json_encode(['status' => 'error', 'message' => 'Degree and semester name are required.']);
            exit;
        }
    
        $stmt = $conn->prepare("INSERT INTO semesters (degree_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $degreeId, $semesterName);
        if ($stmt->execute()) {
            $stmt1 = $conn->prepare("SELECT name FROM degrees WHERE degree_id = ?");
            $stmt1->bind_param("i", $degreeId);
            $stmt1->execute();
            $result = $stmt1->get_result();
            if ($row = $result->fetch_assoc()) $degreeName = $row['name'];
            logActivity($conn, $_SESSION['user_id'], $_SESSION['role'], "Added Semester: $semesterName to Degree : $degreeName");
            echo json_encode(['status' => 'success', 'message' => 'Semester added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add semester.']);
        }
        break;
    
    case 'addSection':
        $degreeId = $_POST['degreeId'];
        $semesterId = $_POST['semesterId'];
        $sectionName = trim($_POST['sectionName']);
        if ($sectionName === '' || !$degreeId || !$semesterId) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }
    
        $stmt = $conn->prepare("INSERT INTO sections (degree_id, semester_id, name) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $degreeId, $semesterId, $sectionName);
        if ($stmt->execute()) {
            $stmt1 = $conn->prepare("SELECT name FROM degrees WHERE degree_id = ?");
            $stmt1->bind_param("i", $degreeId);
            $stmt1->execute();
            $result = $stmt1->get_result();
            if ($row = $result->fetch_assoc()) $degreeName = $row['name'];

            $stmt1 = $conn->prepare("SELECT name FROM semesters WHERE semester_id = ?");
            $stmt1->bind_param("i", $semesterId);
            $stmt1->execute();
            $result = $stmt1->get_result();
            if ($row = $result->fetch_assoc()) $semesterName = $row['name'];
            logActivity($conn, $_SESSION['user_id'], $_SESSION['role'], "Added Section: $sectionName to Semester : $semesterName, Degree : $degreeName");
            echo json_encode(['status' => 'success', 'message' => 'Section added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add section.']);
        }
        break;    
}
?>
