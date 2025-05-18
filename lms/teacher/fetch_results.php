<?php
require_once "../db_connect.php";
require_once "../auth.php";

if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    $query = "SELECT r.result_id, r.student_id, u.username, r.mid, r.final, r.sessional, r.practical, r.grade
              FROM results r
              JOIN students s ON r.student_id = s.student_id
              JOIN users u ON s.user_id = u.user_id
              WHERE r.assignment_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['total'] = $row['mid'] + $row['final'] + $row['sessional'] + $row['practical'];
        $data[] = $row;
    }

    echo json_encode($data);
}
?>
