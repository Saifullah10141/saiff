<?php
require_once "../db_connect.php";
require_once "../auth.php";

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

$sql = "SELECT student_id FROM students WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    $student = $result->fetch_assoc();
    $student_id = $student['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'])) {
    $assignment_id = $_POST['assignment_id'];
    $reg_no = $student_id;  // student's registration number
    $file = $_FILES['assignment_file'];

    // Define allowed max size
    $maxSize = 5 * 1024 * 1024; // 5 MB

    // Validate file
    if ($file['error'] === 0) {

        if ($file['size'] > $maxSize) {
            die("❌ File too large. Max file size is 5 MB.");
        }

        $uploadDir = "../uploads/student_assignments/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = time() . "_" .  rand(1000,9999) . "_" . basename($file['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Check if a submission already exists
            $stmt = $conn->prepare("SELECT * FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?");
            $stmt->bind_param("is", $assignment_id, $reg_no);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                // Update existing submission
                $stmt = $conn->prepare("UPDATE assignment_submissions SET file_path = ?, submitted_at = NOW(), status = 'Not Graded' WHERE assignment_id = ? AND student_id = ?");
                $stmt->bind_param("sis", $filename, $assignment_id, $reg_no);
            } else {
                // Insert new submission
                $stmt = $conn->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_path, submitted_at, status) VALUES (?, ?, ?, NOW(), 'Not Graded')");
                $stmt->bind_param("iss", $assignment_id, $reg_no, $filename);
            }

            $stmt->execute();
            $stmt->close();

            header("Location: assignment_submitted.php");
            exit;
        } else {
            die("❌ File upload failed.");
        }
    } else {
        die("❌ File upload error: " . $file['error']);
    }
} else {
    die("❌ Invalid request.");
}
?>
