<?php
require_once "../db_connect.php";
require_once "../auth.php";

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get instructor_id first (because $user_id is email)
    $stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($instructor_id);
    $stmt->fetch();
    $stmt->close();

    if (!$instructor_id) {
        die("Instructor not found.");
    }

    // Collect posted form data
    $assignment_id = $_POST['subject'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $attendanceData = $_POST['attendance'] ?? [];

    if (empty($assignment_id) || empty($date) || empty($time) || empty($attendanceData)) {
        die("Missing required fields.");
    }

    // 1. Insert into attendances table (for this session)
    $insertAttendance = $conn->prepare("
        INSERT INTO attendances (instructor_id, session_date, assignment_id, session_time)
        VALUES (?, ?, ?, ?)
    ");
    $insertAttendance->bind_param("ssis", $instructor_id, $date, $assignment_id, $time);
    
    if (!$insertAttendance->execute()) {
        die("Failed to create attendance session.");
    }

    // 2. Get the last inserted attendance_id
    $attendance_id = $insertAttendance->insert_id;
    $insertAttendance->close();

    // 3. Insert each student's attendance record
    $success = true;

    foreach ($attendanceData as $student_id => $status) {
        $insertRecord = $conn->prepare("
            INSERT INTO attendance_records (student_id, status, attendance_id)
            VALUES (?, ?, ?)
        ");
        $insertRecord->bind_param("ssi", $student_id, $status, $attendance_id);

        if (!$insertRecord->execute()) {
            $success = false;
        }
        $insertRecord->close();
    }

    // 4. Response
    if ($success) {
        echo "<script>window.location.href='attendance_uploaded.php';</script>";
    } else {
        echo "<script>alert('Some attendance records failed to upload.'); window.location.href='upload_attendance.php';</script>";
    }

} else {
    header("Location: upload_attendance.php");
    exit();
}
?>
