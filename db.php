<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "student_attendance_db"; // Make sure this DB exists

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
