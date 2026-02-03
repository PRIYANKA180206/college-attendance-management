<?php
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$database = "student_attendance_db"; // Make sure this matches your DB name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
