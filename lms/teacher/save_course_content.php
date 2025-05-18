<?php
require_once "../db_connect.php";
require_once "../auth.php";

$user_id = $_SESSION['user_id']; // This is the email

// Get instructor_id from instructors table
$query = "SELECT instructor_id FROM instructors WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($instructor_id);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $assignment_id = $_POST["assignment_id"];
    $week = $_POST["week"];
    $content = $_POST["content"];

    if (!is_numeric($assignment_id) || !is_numeric($week)) {
        http_response_code(400);
        echo "Invalid input.";
        exit;
    }

    // Check if content exists for this week and assignment
    $checkQuery = "SELECT content_id FROM course_contents WHERE assignment_id = ? AND week_no = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $assignment_id, $week);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing content
        $updateQuery = "UPDATE course_contents SET description = ? WHERE assignment_id = ? AND week_no = ? AND instructor_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("siis", $content, $assignment_id, $week, $instructor_id);
        $updateStmt->execute();
    } else {
        // Insert new content
        $insertQuery = "INSERT INTO course_contents (assignment_id, week_no, description, instructor_id) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iiss", $assignment_id, $week, $content, $instructor_id);
        $insertStmt->execute();
    }

    echo "Saved successfully";
}
?>
