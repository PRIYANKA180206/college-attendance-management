<?php
$conn = new mysqli("localhost", "root", "", "student_attendance_db");
if ($conn->connect_error) die("DB connection failed");

$email = $conn->real_escape_string($_POST['email']);
$otp = $conn->real_escape_string($_POST['otp']);

$res = $conn->query("SELECT * FROM password_otps WHERE email='$email' AND otp='$otp' AND expires_at >= NOW()");
if ($res->num_rows === 1) {
    // OTP valid - delete used OTP and redirect to reset page
    $conn->query("DELETE FROM password_otps WHERE email='$email'");
    header("Location: reset_password.php?email=" . urlencode($email));
    exit();
} else {
    die("Invalid or expired OTP.");
}
