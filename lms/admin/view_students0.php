<?php
include '../auth.php';
include '../db_connect.php';

$query = "SELECT 
            s.student_id AS registration_number,
            u.username AS name,
            s.father_name,
            s.cnic,
            u.user_id AS email,
            s.dob,
            s.gender,
            f.name AS faculty_name,
            d.name AS department_name,
            dg.name AS degree_name,
            sem.name AS semester_name,
            sec.name AS section_name
          FROM students s
          JOIN users u ON s.user_id = u.user_id
          JOIN faculties f ON s.faculty_id = f.faculty_id
          JOIN departments d ON s.department_id = d.department_id
          JOIN degrees dg ON s.degree_id = dg.degree_id
          JOIN semesters sem ON s.semester_id = sem.semester_id
          JOIN sections sec ON s.section_id = sec.section_id";


$result = mysqli_query($conn, $query);

// 🔍 Check for SQL errors
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>