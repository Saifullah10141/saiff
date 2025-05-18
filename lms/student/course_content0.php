<?php
require_once '../db_connect.php';
require_once '../auth.php'; // To get $_SESSION['user_id'] (student login info)

// Make sure assignment_id is provided
if (isset($_GET['assignment_id'])) {
    $assignment_id = intval($_GET['assignment_id']);
    $student_id = $_SESSION['user_id']; // Student user_id from session

    // First: Check if the student is enrolled in this assignment
    $checkEnrollmentSql = "SELECT 1
                            FROM enrollments e
                            INNER JOIN students s ON e.student_id = s.student_id
                            WHERE e.assignment_id = '$assignment_id' 
                              AND s.user_id = '$student_id'
                            LIMIT 1";

    $enrollmentResult = $conn->query($checkEnrollmentSql);

    if ($enrollmentResult && $enrollmentResult->num_rows > 0) {
        // ✅ Student is enrolled, now proceed safely

        // Query to get course details (subject name and instructor name)
        $sql = "SELECT asg.name AS subject_name, u.username AS instructor_name
                FROM assigned_subjects asg
                INNER JOIN instructors i ON asg.instructor_id = i.instructor_id
                INNER JOIN users u ON i.user_id = u.user_id
                WHERE asg.assignment_id = '$assignment_id'";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $course = $result->fetch_assoc();
            $subject_name = $course['subject_name'];
            $instructor_name = $course['instructor_name'];
        } else {
            $subject_name = "Unknown Subject";
            $instructor_name = "Unknown Teacher";
        }

        // Query to get course contents week-wise
        $weekSql = "SELECT week_no, description 
                    FROM course_contents 
                    WHERE assignment_id = '$assignment_id' 
                    ORDER BY week_no ASC";
        $weekResult = $conn->query($weekSql);

        $weeks = [];
        if ($weekResult && $weekResult->num_rows > 0) {
            while ($row = $weekResult->fetch_assoc()) {
                $weeks[] = $row;
            }
        }
    } else {
        // ❌ Student not enrolled
        echo "<script>
                alert('Access denied. You are not enrolled in this course.');
                window.location.href = 'enroll_subjects.php'; // Redirect to enrolled subjects page
              </script>";
        exit;    
    }
} else {
    echo "<script>
                alert('No Course Selected');
                window.location.href = 'enroll_subjects.php'; // Redirect to enrolled subjects page
              </script>";;
    exit;
}
?>
