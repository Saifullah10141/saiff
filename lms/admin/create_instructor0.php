<?php
require_once '../db_connect.php';
require_once '../auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form inputs
    $reg_no = $_POST['reg_no'];
    $name = $_POST['name'];
    $father_name = $_POST['father_name'];
    $cnic = $_POST['cnic'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hashing
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $rank = $_POST['rank'];
    $faculty_name = $_POST['faculty'];
    $department_name = $_POST['department'];
    $role = "instructor";

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
         echo "<script>alert('Invalid faculty or department selected.'); window.location.href='create_instructor.php';</script>";
         exit;
     }

    $stmt = $conn->prepare("INSERT INTO users (user_id, username, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $name, $password, $role);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;

        // INSERT into instructors
        $stmt2 = $conn->prepare("INSERT INTO instructors (instructor_id, user_id, father_name, cnic, dob, gender, faculty_id, department_id, rank) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("ssssssiis", $reg_no, $email, $father_name, $cnic, $dob, $gender, $faculty_id, $department_id, $rank);
        if($stmt2->execute()){
            // INSERT user activity
            $admin_id = $_SESSION['user_id'];
            $admin_role_id = $_SESSION['role_id'];
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $action = "Created instructor with Reg. No.: $reg_no";

            $stmt3 = $conn->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
            $userId = $_SESSION['user_id'];
            $action = "Created Instructor ID & Reg. No. : $reg_no";
            $role = $_SESSION['role'];
            $ip_address = getUserIP();
            $stmt3->bind_param("ssss", $userId, $action, $role, $ip_address);
            $stmt3->execute();
            header("Location: instructor_created.php");
            exit();
        }
        else{
            echo "<script>alert('Instructor ID already exists.');  window.location.href='create_instructor.php';</script>";
        }

        // echo "<script>alert('Instructor created successfully.'); window.location.href = 'instructor_created.php';</script>";
    } else {
        echo "<script>alert('Could not create Instructor ID.'); window.location.href='create_instructor.php';</script>";
    }
}
?>
