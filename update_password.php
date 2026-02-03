<?php
$conn = new mysqli("localhost", "root", "", "student_attendance_db");
if ($conn->connect_error) die("DB connection failed");

$email = $conn->real_escape_string($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$conn->query("UPDATE admin_users SET password='$password' WHERE email='$email'");

echo "Password updated successfully! <a href='login.php'>Login Now</a>";
