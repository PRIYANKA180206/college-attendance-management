<?php
$host = "dpg-d60u1u7gi27c73au7k9g-a";
$user = "student_attendance_db_r1pu_user";
$pass = "Xf0dMBMP4ic1Y5JM3uxpwAGzJOTHlbeL";
$db = "student_attendance_db_r1pu"; // Make sure this DB exists

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
