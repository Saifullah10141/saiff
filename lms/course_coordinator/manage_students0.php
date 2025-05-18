<?php
header('Content-Type: application/json');

require_once "../db_connect.php";
require_once "../auth.php";

$user_id = $_SESSION['user_id'];

// Fetch coordinator's department
$sql = "SELECT department_id FROM course_coordinators WHERE user_id = '$user_id'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Coordinator not found']);
    exit();
}
$department = $result->fetch_assoc();
$department_id = $department['department_id'];

// Enroll student logic (when a request is made to enroll)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assignment_id'], $_POST['registration'])) {
    $assignment_id = $_POST['assignment_id'];
    $registration = $_POST['registration'];

    // Check if assignment is valid for this coordinator's department
    $sql = "SELECT a.assignment_id, a.instructor_id
        FROM assigned_subjects a
        INNER JOIN courses c ON a.course_id = c.course_id
        WHERE a.assignment_id = '$assignment_id' 
        AND c.department_id = '$department_id'";

    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        echo json_encode(['error' => 'Invalid Course for this department']);
        exit();
    }

    $assignment = $result->fetch_assoc();
    $instructor_id = $assignment['instructor_id'];

    // Check if the student is already enrolled
    $sql_check_enrollment = "SELECT * FROM enrollments WHERE student_id = '$registration' AND assignment_id = '$assignment_id'";
    $result_check = $conn->query($sql_check_enrollment);

    if ($result_check->num_rows > 0) {
        echo json_encode(['error' => 'Student is already enrolled in this Course']);
        exit();
    }

    // Enroll the student
    $sql = "INSERT INTO enrollments (student_id, assignment_id, instructor_id) 
            VALUES ('$registration', '$assignment_id', '$instructor_id')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => 'Student enrolled successfully!']);
    } else {
        echo json_encode(['error' => 'Failed to enroll student']);
    }
}


// Fetch enrolled students for a selected course
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch students enrolled in the selected course
    $sql = "SELECT 
        s.student_id AS registration_number, 
        u.username AS name, 
        u.user_id AS email
    FROM students s
    JOIN enrollments e ON s.student_id = e.student_id
    JOIN users u ON s.user_id = u.user_id
    WHERE e.assignment_id = '$course_id'";
    $result = $conn->query($sql);
    if (!$result) {
        echo json_encode(['error' => 'Failed to fetch students']);
        exit();
    }

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode(['students' => $students]);
}
?>
