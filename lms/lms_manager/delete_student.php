<?php
require '../db_connect.php';
require '../auth.php';
require_once '../ip.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = $_POST['id'];

    $conn->begin_transaction();

    try {
        // Step 1: Get user_id from students
        $stmt = $conn->prepare("SELECT user_id FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $registration_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Student not found.");
        }

        $user_id = $result->fetch_assoc()['user_id'];
        $stmt->close();

        // Step 2: Delete from students
        $delStudentStmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $delStudentStmt->bind_param("s", $registration_number);
        if (!$delStudentStmt->execute()) {
            throw new Exception("Failed to delete from students.");
        }
        $delStudentStmt->close();

        // Step 3: Delete from users
        $delUserStmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $delUserStmt->bind_param("s", $user_id);
        if (!$delUserStmt->execute()) {
            throw new Exception("Failed to delete from users.");
        }
        $delUserStmt->close();

        // Step 4: Log activity
        $actor_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $ip_address = getUserIp();
        $action = "Deleted Student ID & Reg. No. : $registration_number";

        $logStmt = $conn->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
        $logStmt->bind_param("ssss", $actor_id, $action, $role, $ip_address);
        $logStmt->execute();
        $logStmt->close();

        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        $conn->rollback();
        echo "error";
    }

    $conn->close();
}
?>
