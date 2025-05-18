<?php
require_once '../db_connect.php';
require_once '../auth.php'; // To get $_SESSION['user_id'] (student login info)

// Make sure assignment_id is provided
if (isset($_GET['assignment_id'])) {
    $assignment_id = intval($_GET['assignment_id']);
    $student_id = $_SESSION['user_id']; // Student user_id from session

    // First: Check if the student is enrolled in this assignment using prepared statements
    $checkEnrollmentSql = "SELECT s.student_id
                            FROM enrollments e
                            INNER JOIN students s ON e.student_id = s.student_id
                            WHERE e.assignment_id = ? 
                            AND s.user_id = ? 
                            LIMIT 1";

    if ($stmt = $conn->prepare($checkEnrollmentSql)) {
        $stmt->bind_param("is", $assignment_id, $student_id); // Bind parameters
        $stmt->execute();
        $enrollmentResult = $stmt->get_result();

        // Check if the student is enrolled
        if ($enrollmentResult && $enrollmentResult->num_rows > 0) {
            $enrollment = $enrollmentResult->fetch_assoc();
            $student_id = $enrollment['student_id'];
            // ✅ Student is enrolled
            
            // Fetch subject and instructor info
            $subject_sql = "SELECT asg.name AS subject_name, u.username AS instructor_name
                            FROM assigned_subjects asg
                            INNER JOIN instructors i ON asg.instructor_id = i.instructor_id
                            INNER JOIN users u ON i.user_id = u.user_id
                            WHERE asg.assignment_id = ?";
            
            if ($stmt = $conn->prepare($subject_sql)) {
                $stmt->bind_param("i", $assignment_id);
                $stmt->execute();
                $subject_result = $stmt->get_result();
                
                if ($subject_result && $subject_result->num_rows > 0) {
                    $subject_data = $subject_result->fetch_assoc();
                    $subject_name = $subject_data['subject_name'];
                    $instructor_name = $subject_data['instructor_name'];
                } else {
                    $subject_name = "Unknown Subject";
                    $instructor_name = "Unknown Instructor";
                }

                // Fetch attendance records
                $attendance_sql = "SELECT a.session_date, a.session_time, ar.status 
                FROM attendance_records ar
                INNER JOIN attendances a ON ar.attendance_id = a.attendance_id
                WHERE ar.student_id = ? AND a.assignment_id = ?
                ORDER BY a.session_date DESC, a.session_time DESC";
                
                if ($stmt = $conn->prepare($attendance_sql)) {
                    $stmt->bind_param("si", $student_id, $assignment_id);
                    $stmt->execute();
                    $attendance_result = $stmt->get_result();

                    $attendance_records = [];
                    $present_count = 0;
                    $total_count = 0;

                    if ($attendance_result && $attendance_result->num_rows > 0) {
                        while ($row = $attendance_result->fetch_assoc()) {
                            $attendance_records[] = $row;
                                $total_count++;
                                if ($row['status'] == 'Present') {
                                    $present_count++;
                                }
                        }
                        // Calculate attendance percentage
                        $attendance_percentage = $total_count > 0 ? round(($present_count / $total_count) * 100, 2) : 0;
                    }
                    else{
                        $attendance_percentage = "NAN ";
                    }

                } else {
                    echo "Error fetching attendance records: " . $conn->error;
                    exit;
                }

            } else {
                echo "Error fetching subject data: " . $conn->error;
                exit;
            }

        } else {
            echo "<script>
                    alert('Access denied. You are not enrolled in this course.');
                    window.location.href = 'enroll_subjects.php'; // Redirect to enrolled subjects page
                  </script>";
            exit;    
        }

    } else {
        echo "Error checking enrollment: " . $conn->error;
        exit;
    }

} else {
    echo "<script>
            alert('No Course Selected');
            window.location.href = 'enroll_subjects.php'; // Redirect to enrolled subjects page
          </script>";
    exit;
}
?>
