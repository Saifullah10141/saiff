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

    case 'getDegrees':
        $stmt = $conn->prepare("SELECT department_id FROM lms_managers WHERE user_id = ?");
        $stmt->bind_param("s", $_SESSION['user_id']); // assuming email is used as ID
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $_SESSION['department_id'] = $row['department_id'];
        }
        $departmentId = $_SESSION['department_id']; // get from session
        $stmt = $conn->prepare("SELECT degree_id, name FROM degrees WHERE department_id = ?");
        $stmt->bind_param("i", $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $degrees = [];
        while ($row = $result->fetch_assoc()) {
            $degrees[] = $row;
        }
        echo json_encode(['status' => 'success', 'degrees' => $degrees]);
        break;    

    case 'getSemesters':
        $degreeId = $_POST['degreeId'];
        $stmt = $conn->prepare("SELECT semester_id, name FROM semesters WHERE degree_id = ?");
        $stmt->bind_param("i", $degreeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $semesters = [];
        while ($row = $result->fetch_assoc()) {
            $semesters[] = $row;
        }
        echo json_encode(['status' => 'success', 'semesters' => $semesters]);
        break;

    case 'getSections':
        $degreeId = $_POST['degreeId'];
        $semesterId = $_POST['semesterId'];
        $stmt = $conn->prepare("SELECT section_id, name FROM sections WHERE degree_id = ? AND semester_id = ?");
        $stmt->bind_param("ii", $degreeId, $semesterId);
        $stmt->execute();
        $result = $stmt->get_result();
        $sections = [];
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row;
        }
        echo json_encode(['status' => 'success', 'sections' => $sections]);
        break;

        case 'delete':
            $type = $_POST['type'];
            $id = $_POST['id'];
        
            // Map type to table and column
            $map = [
                'Degree' => ['table' => 'degrees', 'column' => 'degree_id'],
                'Semester' => ['table' => 'semesters', 'column' => 'semester_id'],
                'Section' => ['table' => 'sections', 'column' => 'section_id']
            ];
        
            if (!isset($map[$type])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid type']);
                break;
            }
        
            $table = $map[$type]['table'];
            $column = $map[$type]['column'];
        
            $stmt1 = $conn->prepare("SELECT name FROM $table WHERE $column = ?");
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            $result = $stmt1->get_result();
            if ($row = $result->fetch_assoc()) $degreeName = $row['name'];

            $stmt = $conn->prepare("DELETE FROM $table WHERE $column = ?");
            $stmt->bind_param("i", $id);
        
            if ($stmt->execute()) {
                logActivity($conn, $_SESSION['user_id'], $_SESSION['role'], "Deleted $type : $degreeName");
                echo json_encode(['status' => 'success', 'message' => "$type deleted."]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Failed to delete $type."]);
            }
            break;        

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
