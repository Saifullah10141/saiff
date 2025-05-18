<?php
// Include database connection
require_once '../db_connect.php';  // Adjust the path to your db connection file
require_once '../auth.php';

// Fetch all departments
$query = "SELECT name FROM departments";  // Assuming your department table has a 'name' column
$result = mysqli_query($conn, $query);

if ($result) {
    $departments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }

    // Return departments as JSON
    echo json_encode($departments);
} else {
    // Handle query failure
    echo json_encode(['error' => 'Failed to fetch departments']);
}

// Close the connection
mysqli_close($conn);
?>
