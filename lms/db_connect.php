<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "lms"; // or whatever your DB name is

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    header("Location: /saiff/LMS/db_fail.php");
}
?>
