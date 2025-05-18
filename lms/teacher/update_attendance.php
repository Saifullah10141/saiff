<?php
require_once "../db_connect.php";
require_once "../auth.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_id = intval($_POST['attendance_id']);
    $registration_no = $_POST['registration_no'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("
        UPDATE attendance_records ar
        INNER JOIN students s ON ar.student_id = s.student_id
        SET ar.status = ?
        WHERE ar.attendance_id = ? AND s.student_id = ?
    ");
    $stmt->bind_param("sis", $status, $attendance_id, $registration_no);

    if ($stmt->execute()) {
        echo "<script>alert('Attendance updated successfully.'); window.location.href='view_attendance.php?id=$attendance_id';</script>";
    } else {
        echo "<script>alert('Failed to update attendance.'); window.location.href='view_attendance.php?id=$attendance_id';</script>";
    }
    $stmt->close();
} else {
    header("Location: view_attendance.php");
    exit();
}
?>
