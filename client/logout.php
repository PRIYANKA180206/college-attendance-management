<?php
session_start();
// Unset all session variables
session_unset();
// Destroy the session
session_destroy();
// Redirect to login page
header("Location: student_login.php");
exit();
?>
