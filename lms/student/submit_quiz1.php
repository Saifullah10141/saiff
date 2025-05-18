<?php
include '../db_connect.php';
include '../auth.php';

$user_id = $_SESSION['user_id'];

// Get student_id from user_id
$sql = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['student_id'];
$stmt->close();

$quiz_id = $_POST['quiz_id'];

// Get quiz time_limit and student's started_at
$sql = "SELECT qs.started_at, qs.submission_id, qs.submitted_at, q.time_limit
        FROM quiz_submissions qs
        JOIN quizzes q ON q.quiz_id = qs.quiz_id
        WHERE qs.quiz_id = ? AND qs.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $quiz_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Quiz attempt not found.");
}

// Check if already submitted
if ($data['submitted_at'] !== null) {
    echo "<script>
    alert('You have already submitted this quiz.');
    window.location.href = 'quiz.php';
</script>";
exit;
}

$started_at = strtotime($data['started_at']);
$time_limit_seconds = $data['time_limit'] * 60;
$now = time();

if (($now - $started_at) > $time_limit_seconds-10800) {
    echo "<script>
    alert('Time limit exceeded. Quiz cannot be submitted.');
    window.location.href = 'quiz.php';
</script>";
exit;
}

// Extract answers from POST
$answers = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') === 0) {
        $question_id = str_replace('question_', '', $key);
        $answers[$question_id] = $value;
    }
}

// Calculate score
$score = 0;

// Assuming $quiz_id is already available
$sql = "SELECT COUNT(*) AS total FROM quiz_questions WHERE quiz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$total_questions = $result['total'] ?? 0;

foreach ($answers as $question_id => $selected_option_letter) {
    // Step 1: Get all option texts and the correct answer
    $sql = "SELECT correct_answer, option1, option2, option3 FROM quiz_questions WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($result) {
        // Step 2: Map option letters to texts
        $options = [
            'A' => $result['correct_answer'],
            'B' => $result['option1'],
            'C' => $result['option2'],
            'D' => $result['option3']
        ];

        // Step 3: Compare selected answer text with the correct one
        $selected_text = $options[$selected_option_letter];
        $correct_text = $result['correct_answer'];

        if ($selected_text === $correct_text) {
            $score++;
        }
    }
}


$percentage = ($total_questions > 0) ? ($score / $total_questions) * 100 : 0;

// Update quiz_submissions with score and submitted_at
$update_sql = "UPDATE quiz_submissions SET submitted_at = NOW(), score = ? WHERE quiz_id = ? AND student_id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("dis", $percentage, $quiz_id, $student_id);
$stmt->execute();
$stmt->close();

// Get submission_id for quiz_answers
$submission_id = $data['submission_id'];
if (!$submission_id) {
    // Just in case it's not present
    $stmt = $conn->prepare("SELECT submission_id FROM quiz_submissions WHERE quiz_id = ? AND student_id = ?");
    $stmt->bind_param("is", $quiz_id, $student_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $submission_id = $res['submission_id'];
    $stmt->close();
}

// Insert quiz_answers
// foreach ($answers as $question_id => $selected_option) {
//     $answer_sql = "INSERT INTO quiz_answers (submission_id, question_id, selected_option) VALUES (?, ?, ?)";
//     $stmt = $conn->prepare($answer_sql);
//     $stmt->bind_param("iis", $submission_id, $question_id, $selected_option);
//     $stmt->execute();
//     $stmt->close();
// }

echo "<script>
    window.location.href = 'quiz_submitted.php?score=$percentage';
</script>";

?>
