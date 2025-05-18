<?php
require_once "../db_connect.php";
require_once "../auth.php";

if (isset($_GET['id'])) {
    $attendance_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id']; // email

    // Get the instructor_id based on the logged-in user
    $stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($instructor_id);
    $stmt->fetch();
    $stmt->close();

    if (!$instructor_id) {
        echo "<script>alert('Instructor not found.'); window.location.href='attendance.php';</script>";
        exit();
    }

    // Now check if this attendance belongs to the instructor
    $stmt = $conn->prepare("SELECT attendance_id FROM attendances WHERE attendance_id = ? AND instructor_id = ?");
    $stmt->bind_param("is", $attendance_id, $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Not authorized to delete
        echo "<script>alert('Unauthorized access. You cannot delete this attendance.'); window.location.href='uploaded_attendances.php';</script>";
        exit();
    }
    $stmt->close();

    // Now safe to delete
    $stmt = $conn->prepare("DELETE FROM attendances WHERE attendance_id = ?");
    $stmt->bind_param("i", $attendance_id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='attendance_deleted.php';</script>";
    } else {
        echo "<script>alert('Failed to delete attendance.'); window.location.href='uploaded_attendances.php';</script>";
    }
    $stmt->close();
} else {
    header("Location: uploaded_attendances.php");
    exit();
}
?>
