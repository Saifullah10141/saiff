<?php
require_once '../db_connect.php';
require_once '../auth.php';
require_once 'get_manager_data.php';

$flag = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $reg_no = $_POST['reg_no'];
    $name = $_POST['name'];
    $father_name = $_POST['father_name'];
    $cnic = $_POST['cnic'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $degree_id = $_POST['degree'];
    $semester_id = $_POST['semester'];
    $section_id = $_POST['section'];
    $role = 'student';

    if (!isset($error)) {
        // Insert into users table
        $user_stmt = $conn->prepare("INSERT INTO users (user_id, username, password_hash, role) VALUES (?, ?, ?, ?)");
        if (!$user_stmt) {
            error_log("User insert prepare failed: " . $conn->error);
            $error = true;
        } else {
            $user_stmt->bind_param("ssss", $email, $name, $password, $role);
            if ($user_stmt->execute()) {
                // Insert into students table
                $cc_stmt = $conn->prepare("INSERT INTO students (student_id, user_id, father_name, cnic, dob, gender, faculty_id, department_id, degree_id, semester_id, section_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$cc_stmt) {
                    error_log("Student insert prepare failed: " . $conn->error);
                    $error = true;
                } else {
                    $cc_stmt->bind_param("ssssssiiiii", $reg_no, $email, $father_name, $cnic, $dob, $gender, $faculty_id, $department_id, $degree_id, $semester_id, $section_id);
                    if ($cc_stmt->execute()) {
                        $flag = true;
                        // Log user activity
                        $activity_stmt = $conn->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
                        if ($activity_stmt) {
                            $userId = $_SESSION['user_id'];       // Assuming session contains user_id
                            $action = "Created Student ID & Reg. No. : $reg_no";
                            $role = $_SESSION['role'];            // Assuming session contains role
                            $ip_address = getUserIP();         // From your ip.php file

                            $activity_stmt->bind_param("ssss", $userId, $action, $role, $ip_address);
                            $activity_stmt->execute();
                        } else {
                            error_log("Failed to log user activity: " . $conn->error);
                        }

                        header("Location: student_created.php");
                        exit();
                    }
                }
            }
        }
    }
}
if(!$flag){
    echo "<script>alert('Student ID already exists.');  window.location.href='create_student.php';</script>";
}
echo "<script>alert('Could not create Student ID.'); window.location.href='create_student.php';</script>";
?>
