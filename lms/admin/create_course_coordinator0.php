<?php
require_once '../db_connect.php';
require_once '../auth.php';

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
    $faculty_name = $_POST['faculty'];
    $department_name = $_POST['department'];
    $role = 'course_coordinator';

     // Fetch faculty_id
     $stmt = $conn->prepare("SELECT faculty_id FROM faculties WHERE name = ?");
     $stmt->bind_param("s", $faculty_name);
     $stmt->execute();
     $result = $stmt->get_result();
     $faculty_data = $result->fetch_assoc();
     $faculty_id = $faculty_data ? $faculty_data['faculty_id'] : null;
 
     // Fetch department_id
     $stmt = $conn->prepare("SELECT department_id FROM departments WHERE name = ?");
     $stmt->bind_param("s", $department_name);
     $stmt->execute();
     $result = $stmt->get_result();
     $department_data = $result->fetch_assoc();
     $department_id = $department_data ? $department_data['department_id'] : null;
 
     if (!$faculty_id || !$department_id) {
         echo "<script>alert('Invalid faculty or department selected.'); window.location.href='create_course_coordinator.php';</script>";
         exit;
     }

    if (!isset($error)) {
        // Insert into users table
        $user_stmt = $conn->prepare("INSERT INTO users (user_id, username, password_hash, role) VALUES (?, ?, ?, ?)");
        if (!$user_stmt) {
            error_log("User insert prepare failed: " . $conn->error);
            $error = true;
        } else {
            $user_stmt->bind_param("ssss", $email, $name, $password, $role);
            if ($user_stmt->execute()) {
                // Insert into course_coordinators table
                $cc_stmt = $conn->prepare("INSERT INTO course_coordinators (coordinator_id, user_id, father_name, cnic, dob, gender, faculty_id, department_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$cc_stmt) {
                    error_log("Coordinator insert prepare failed: " . $conn->error);
                    $error = true;
                } else {
                    $cc_stmt->bind_param("ssssssii", $reg_no, $email, $father_name, $cnic, $dob, $gender, $faculty_id, $department_id);
                    if ($cc_stmt->execute()) {
                        $flag = true;
                        // Log user activity
                        $activity_stmt = $conn->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
                        if ($activity_stmt) {
                            $userId = $_SESSION['user_id'];       // Assuming session contains user_id
                            $action = "Created course coordinator ID & Reg. No. : $reg_no";
                            $role = $_SESSION['role'];            // Assuming session contains role
                            $ip_address = getUserIP();         // From your ip.php file

                            $activity_stmt->bind_param("ssss", $userId, $action, $role, $ip_address);
                            $activity_stmt->execute();
                        } else {
                            error_log("Failed to log user activity: " . $conn->error);
                        }

                        header("Location: course_coordinator_created.php");
                        exit();
                    }
                }
            }
        }
    }
}
if(!$flag){
    echo "<script>alert('Course Coordinator ID already exists.');  window.location.href='create_course_coordinator.php';</script>";
}
echo "<script>alert('Could not create Course Coordinator ID.'); window.location.href='create_course_coordinator.php';</script>";
?>
