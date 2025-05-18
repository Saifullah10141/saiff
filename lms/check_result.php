<?php
require_once "db_connect.php";

$student_id = $_POST['student_id'] ?? null;
$results = [];

if ($student_id) {
    $stmt = $conn->prepare("SELECT r.*, c.course_name, u.username AS instructor_name, c.course_id, c.credit_hours_theory as ct, c.credit_hours_practical as cp
            FROM results r
            JOIN assigned_subjects a ON r.assignment_id = a.assignment_id
            JOIN courses c ON a.course_id = c.course_id
            JOIN instructors i ON a.instructor_id = i.instructor_id
            JOIN users u ON i.user_id = u.user_id
            WHERE r.student_id = ?
            ");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $results = $stmt->get_result();
    $stmt->close();

    $stmt = $conn->prepare("SELECT u.username 
                        FROM students s
                        JOIN users u ON s.user_id = u.user_id 
                        WHERE s.student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgba(255, 255, 255, 0.1);
            background-image: radial-gradient(circle at top left, rgba(173, 216, 230, 0.2), transparent), 
                              radial-gradient(circle at bottom right, rgba(135, 206, 250, 0.3), transparent);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .glass-box {
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 900px;
            color: #003344;
        }

        h3 {
            color: #003b5c;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
        }

        table th, table td {
            background-color: rgba(255, 255, 255, 0.15);
            color: #002233;
        }

        .btn-back {
            background-color: #3399cc;
            color: white;
            font-weight: bold;
            border-radius: 25px;
            padding: 10px 20px;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-back:hover {
            background-color: #66d3fa;
        }

        .no-result {
            color: #c00;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="glass-box text-center">
        <?php if ($student_id): ?>
            <h3>Result Award List</h3>

            <hr>

            <h4>
                <?php
                if($student_name){
                ?>
                Student Name : <?= htmlspecialchars($student_name) ?> <br>
                <?php
                }
                ?>
                Registration number: <?= htmlspecialchars($student_id) ?>
            </h4>

            <br>

            <?php if ($results && $results->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover rounded shadow-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Instructor</th>
                                <th>Credit Hours</th>
                                <th>Mid</th>
                                <th>Final</th>
                                <th>Sessional</th>
                                <th>Practical</th>
                                <th>Total</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
                                function clean($num) {
                                    return rtrim(rtrim(number_format($num, 2), '0'), '.');
                                }
                                ?>
                        <?php while ($row = $results->fetch_assoc()): ?>
                            <tr>
                                <?php
                                $total = $row['mid'] + $row['final'] + $row['sessional'] + $row['practical'];
                                ?>
                                <td><?= htmlspecialchars($row['course_id']) ?></td>
                                <td><?= htmlspecialchars($row['course_name']) ?></td>
                                <td><?= htmlspecialchars($row['instructor_name']) ?></td>
                                <td><?= clean($row['ct'])+clean($row['cp']) . "(" . clean($row['ct']) . "-" . clean($row['cp']) . ")" ?></td>
                                <td><?= clean($row['mid']) ?></td>
                                <td><?= clean($row['final']) ?></td>
                                <td><?= clean($row['sessional']) ?></td>
                                <td><?= clean($row['practical']) ?></td>
                                <td><?= clean($total) ?></td>
                                <td><strong><?= $row['grade'] ?></strong></td>

                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-result">No results found for this registration number.</p>
            <?php endif; ?>
            <br>
            <a href="login.php" class="btn-back">Back</a>
        <?php else: ?>
            <p class="no-result">No registration number submitted.</p>
            <a href="login.php" class="btn-back">Go Back</a>
        <?php endif; ?>
    </div>
</body>
</html>
