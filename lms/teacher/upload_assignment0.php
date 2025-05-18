<?php
require_once "../db_connect.php";
require_once "../auth.php";

$user_id = $_SESSION['user_id'];
$username = "";

// Fetch instructor_id
$stmt = $conn->prepare("SELECT instructor_id FROM instructors WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($instructor_id);
$stmt->fetch();
$stmt->close();

// Fetch assigned subjects
$subjects = [];
$stmt = $conn->prepare("SELECT assignment_id, name FROM assigned_subjects WHERE instructor_id = ?");
$stmt->bind_param("s", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $subject_id = $_POST['subject'];

    // Optional: handle file upload
    $filename = "";
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $originalName = basename($_FILES['file']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newName = pathinfo($originalName, PATHINFO_FILENAME); // get file name without extension

        // Create a unique filename
        $uniqueName = $newName . "_" . time() . "_" . rand(1000,9999) . "." . $extension;

        move_uploaded_file($_FILES['file']['tmp_name'], "../uploads/instructor_assignments/" . $uniqueName);
        $filename = $uniqueName;
    }

    $stmt = $conn->prepare("INSERT INTO assignments (instructor_id, title, description, due_date, file_path, assignment_id1) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $instructor_id, $title, $description, $due_date, $filename, $subject_id);
    $stmt->execute();
    $stmt->close();
    ?>
<script>alert("Assignment successfully uploaded!");</script>
<?php
}
?>