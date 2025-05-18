<?php
require '../db_connect.php';
require '../auth.php';
require_once '../ip.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['coordinator_id'];
    $name = $_POST['name'];
    $father_name = $_POST['father_name'];
    $cnic = $_POST['cnic'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $department = $_POST['editDepartment'];

    // Fetch faculty_id and department_id based on the department
    $stmt = $conn->prepare("SELECT faculty_id, department_id FROM departments WHERE name=? LIMIT 1");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $stmt->bind_result($faculty_id, $department_id);
    $stmt->fetch();
    $stmt->close();
    
    if ($faculty_id && $department_id) {
        // Update course coordinator
        $stmt = $conn->prepare("UPDATE lms_managers SET user_id=?, father_name=?, cnic=?, dob=?, gender=?, faculty_id=?, department_id=? WHERE manager_id=?");
        $stmt->bind_param("sssssiis", $email, $father_name, $cnic, $dob, $gender, $faculty_id, $department_id, $id);

        $stmt2 = $conn->prepare("UPDATE users SET username=? WHERE user_id=?");
        $stmt2->bind_param("ss",$name , $email);

        if (($stmt->execute()) && ($stmt2->execute())) {
            // Log activity
            $user_id = $_SESSION['user_id'];
            $role = $_SESSION['role'];
            $ip = getUserIp();
            $action = "Updated LMS Manager & Reg. No. : $id";
            $log = $conn->prepare("INSERT INTO user_activities (user_id, role, action, ip_address) VALUES (?, ?, ?, ?)");
            $log->bind_param("ssss", $user_id, $role, $action, $ip);
            $log->execute();

            echo "updated";
        } else {
            echo "error";
        }

        $stmt->close();
    } else {
        echo "Faculty or department not found for the selected department.";
    }
}
?>
