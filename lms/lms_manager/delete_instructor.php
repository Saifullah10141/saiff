<?php
require_once '../db_connect.php';
require_once '../auth.php';
require_once '../ip.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reg_no = $_POST['reg_no']; // This is coordinator_id

    // Get user_id linked to this course coordinator
    $stmt = $conn->prepare("SELECT user_id FROM instructors WHERE instructor_id = ?");
    $stmt->bind_param("s", $reg_no);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Delete from course_coordinators
        $stmt1 = $conn->prepare("DELETE FROM instructors WHERE instructor_id = ?");
        $stmt1->bind_param("s", $reg_no);
        $stmt1->execute();

        // Delete from users
        $stmt2 = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt2->bind_param("s", $user_id);
        $stmt2->execute();

        // Log the delete action
        $admin_id = $_SESSION['user_id'];
        $role = $_SESSION['role']; // e.g. 'admin'
        $action = "Deleted Instructor ID & Reg. No. : $reg_no";
        $ip_address = getUserIp();

        $stmt3 = $conn->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
        $stmt3->bind_param("ssss", $admin_id, $action, $role, $ip_address);
        $stmt3->execute();

        echo "deleted";
    } else {
        echo "not_found";
    }
}
?>
