<?php
require_once "../db_connect.php";
require_once "../auth.php";

// Check if the form is being submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];

    // Fetch students enrolled in the assignment
    $stmt = $conn->prepare("
    SELECT s.student_id AS registration_number, u.username AS full_name, u.user_id AS email
    FROM enrollments e
    JOIN students s ON e.student_id = s.student_id
    JOIN users u ON s.user_id = u.user_id
    WHERE e.assignment_id = ?
");



    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $studentsResult = $stmt->get_result();

    // Check if there are students enrolled
    if ($studentsResult->num_rows > 0) {
        while ($student = $studentsResult->fetch_assoc()) {
            $student_name = $student['full_name'];
            $registration_number = $student['registration_number'];

            // Display student information and attendance options
            echo "<tr>
                <td>" . htmlspecialchars($student_name) . "</td>
                <td>" . htmlspecialchars($registration_number) . "</td>
                <td class='attendance-options'>
                    <label>
                        <input type='radio' name='attendance[" . htmlspecialchars($registration_number) . "]' value='Present' required> P
                    </label>
                    <label>
                        <input type='radio' name='attendance[" . htmlspecialchars($registration_number) . "]' value='Absent'> A
                    </label>
                    <label>
                        <input type='radio' name='attendance[" . htmlspecialchars($registration_number) . "]' value='Leave'> L
                    </label>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No students enrolled.</td></tr>";
    }
}
?>
