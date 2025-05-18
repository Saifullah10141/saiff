<?php
require_once "../db_connect.php";
require_once "../auth.php";

header('Content-Type: application/json');
$instructor_email = $_SESSION['user_id'] ?? null;

if (!$instructor_email) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Get instructor_id
$stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
$stmt->bind_param("s", $instructor_email);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();
$instructor_id = $instructor['instructor_id'] ?? null;

if (!$instructor_id) {
    echo json_encode(["error" => "Instructor not found"]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'get_assignments') {
    // Fetch instructor's assigned subjects
    $stmt = $conn->prepare("SELECT assignment_id, name FROM assigned_subjects WHERE instructor_id = ?");
    $stmt->bind_param("s", $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $assignments = [];
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }

    echo json_encode($assignments);
    exit;
}

if ($action === 'enroll_student') {
    $assignment_id = $_POST['assignment_id'] ?? null;
    $reg_no = $_POST['registration_number'] ?? null;

    if (!$assignment_id || !$reg_no) {
        echo json_encode(["error" => "Missing data"]);
        exit;
    }

    // Check if student exists
    $stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $reg_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        echo json_encode(["error" => "Student not found"]);
        exit;
    }

    $student_id = $student['student_id'];

    // Prevent duplicate enrollment
    $check = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND assignment_id = ?");
    $check->bind_param("si", $student_id, $assignment_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(["error" => "Student already enrolled"]);
        exit;
    }

    // Enroll student
    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, instructor_id, assignment_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $student_id, $instructor_id, $assignment_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to enroll student"]);
    }
    exit;
}

if ($action === 'get_students') {
    $assignment_id = $_GET['assignment_id'] ?? null;

    if (!$assignment_id) {
        echo json_encode([]);
        exit;
    }

    $stmt = $conn->prepare("
    SELECT s.student_id AS registration_number, u.username AS full_name, u.user_id AS email
    FROM enrollments e
    JOIN students s ON e.student_id = s.student_id
    JOIN users u ON s.user_id = u.user_id
    WHERE e.assignment_id = ?
");

    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode($students);
    exit;
}
?>
