<?php
require_once "../db_connect.php";
require_once "../auth.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_POST['student_id'];
    $mid = $_POST['mid'];
    $final = $_POST['final'];
    $sessional = $_POST['sessional'];
    $practical = $_POST['practical'];

    // Get course_id from assigned_subjects
    $stmt = $conn->prepare("SELECT course_id FROM assigned_subjects WHERE assignment_id = ?");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $stmt->bind_result($course_id);
    $stmt->fetch();
    $stmt->close();

    // Fetch credit hours for theory and practical
    $creditStmt = $conn->prepare("SELECT credit_hours_theory, credit_hours_practical FROM courses WHERE course_id = ?");
    $creditStmt->bind_param("s", $course_id);
    $creditStmt->execute();
    $creditStmt->bind_result($theory_hours, $practical_hours);
    $creditStmt->fetch();
    $creditStmt->close();

    // Calculate total marks
    $theory_total_marks = $theory_hours * 20;
    $practical_total_marks = $practical_hours * 20;
    $total_marks = $theory_total_marks + $practical_total_marks;

    // Calculate weighted theory score
    $theory_weighted = ((($mid+$final) * 100) / $theory_total_marks) / 0.90;
    $sessional_weighted = ($sessional * 100) / $theory_total_marks * 0.10;
    $practical_weighted = ($practical * 100) / $practical_total_marks;

    // Total obtained marks
    $obtained = $mid + $final + $sessional + $practical;

    // Calculate percentages for checks
    $theory_percentage = $theory_weighted;
    $practical_percentage = $practical_weighted;
    $overall_percentage = ($obtained / $total_marks) * 100;

    // Check for F grade based on mid/final/practical (NOT sessional)
    if ($theory_percentage < 40 || ($practical_hours > 0 && $practical_percentage < 40)) {
        $grade = 'F';
    } else {
        if ($overall_percentage >= 80) {
            $grade = 'A';
        } elseif ($overall_percentage >= 65) {
            $grade = 'B';
        } elseif ($overall_percentage >= 50) {
            $grade = 'C';
        } elseif ($overall_percentage >= 40) {
            $grade = 'D';
        } else {
            $grade = 'F';
        }
    }

    // Check if result already exists for this student and course
    $check = $conn->prepare("SELECT result_id FROM results WHERE student_id = ? AND assignment_id = ?");
    $check->bind_param("ss", $student_id, $assignment_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "exists";
        exit;
    }

    $check->close();

    // Insert result
    $insert = $conn->prepare("INSERT INTO results (student_id, assignment_id, course_id, mid, final, sessional, practical, grade) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("sisdddds", $student_id, $assignment_id, $course_id, $mid, $final, $sessional, $practical, $grade);

    if ($insert->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $insert->close();
}
?>
