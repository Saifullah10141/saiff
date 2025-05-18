<?php
require '../db_connect.php';
require '../auth.php';
require_once '../ip.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = $_POST['id'];
    $name = $_POST['name'];
    $father_name = $_POST['father_name'];
    $cnic = $_POST['cnic'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $faculty_id = $_POST['faculty'];
    $department_id = $_POST['department'];
    $degree_id = $_POST['degree'];
    $semester_id = $_POST['semester'];
    $section_id = $_POST['section'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Step 1: Get user_id from students table using registration_number
        $getUserIdStmt = $conn->prepare("SELECT user_id FROM students WHERE student_id = ?");
        $getUserIdStmt->bind_param("s", $registration_number);
        $getUserIdStmt->execute();
        $result = $getUserIdStmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Student not found.");
        }

        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $getUserIdStmt->close();

        // Step 2: Update users table
        $updateUserStmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
        $updateUserStmt->bind_param("ss", $name, $email);
        if (!$updateUserStmt->execute()) {
            throw new Exception("Failed to update users table.");
        }
        $updateUserStmt->close();

        // Step 3: Update students table
        $updateStudentStmt = $conn->prepare("UPDATE students SET father_name = ?, cnic = ?, dob = ?, gender = ?, 
            faculty_id = ?, department_id = ?, degree_id = ?, semester_id = ?, section_id = ?
            WHERE student_id = ?");
        $updateStudentStmt->bind_param(
            "ssssiiiiis",
            $father_name, $cnic, $dob, $gender,
            $faculty_id, $department_id, $degree_id, $semester_id, $section_id, $registration_number
        );
        if (!$updateStudentStmt->execute()) {
            throw new Exception("Failed to update students table.");
        }
        $updateStudentStmt->close();

        // Step 4: Log the activity
        $actor_id = $_SESSION['user_id']; // who performed the action
        $role = $_SESSION['role'];        // user's role, e.g., 'admin'
        $ip_address = getUserIp();
        $action = "Updated Student & Reg. No. : $registration_number";

        $logStmt = $conn->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
        $logStmt->bind_param("ssss", $actor_id, $action, $role, $ip_address);
        $logStmt->execute();
        $logStmt->close();

        // Commit all changes
        $conn->commit();
        echo "success";

    } catch (Exception $e) {
        $conn->rollback();
        echo "error";
    }

    $conn->close();
}
?>
