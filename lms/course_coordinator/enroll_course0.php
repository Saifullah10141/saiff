<?php
require_once "../db_connect.php";
require_once "../auth.php";

// Assuming the current logged-in user's user_id is stored in session
$user_id = $_SESSION['user_id'];  // Get the user_id from session (email)

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get course data from the POST request
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $credit_hours_theory = $_POST['credit_hours_theory'];
    $credit_hours_practical = $_POST['credit_hours_practical'];

    // Check if all fields are filled
    if (empty($course_name) || empty($course_code)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
        exit;
    }

    // Fetch coordinator_id and department_id based on the user_id
    $fetch_coordinator_sql = "SELECT cc.coordinator_id, cc.department_id 
                              FROM course_coordinators cc 
                              WHERE cc.user_id = ?";
    if ($stmt = $conn->prepare($fetch_coordinator_sql)) {
        $stmt->bind_param("s", $user_id); // Bind the user_id (email)
        $stmt->execute();
        $stmt->bind_result($coordinator_id, $department_id);
        $stmt->fetch();
        $stmt->close();

        if (!$coordinator_id || !$department_id) {
            echo json_encode(['status' => 'error', 'message' => 'No coordinator or department found for the current user.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error while fetching coordinator and department.']);
        exit;
    }

    // Check if the course already exists
    $check_sql = "SELECT COUNT(*) FROM courses WHERE course_id = ?";
    if ($stmt = $conn->prepare($check_sql)) {
        $stmt->bind_param("s", $course_code);
        $stmt->execute();
        $stmt->bind_result($course_count);
        $stmt->fetch();
        $stmt->close();

        if ($course_count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This course is already enrolled.']);
            exit;
        }
    }

    // Prepare the SQL query to insert the new course into the database
    $sql = "INSERT INTO courses (course_name, course_id, credit_hours_theory, credit_hours_practical, coordinator_id, department_id) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters and execute the statement
        $stmt->bind_param("ssiiss", $course_name, $course_code, $credit_hours_theory, $credit_hours_practical, $coordinator_id, $department_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course enrolled successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error enrolling course.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error while inserting course.']);
    }

    // Close the connection
    $conn->close();
}
?>
