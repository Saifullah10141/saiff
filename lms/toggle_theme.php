<?php
session_start();
if (isset($_SESSION['dark_mode'])) {
    $_SESSION['dark_mode'] = !$_SESSION['dark_mode']; // Toggle
}
header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back
exit();
?>
