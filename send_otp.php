<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$conn = new mysqli("localhost", "root", "", "student_attendance_db");
if ($conn->connect_error) {
    die("Database connection failed");
}

// ✅ PHP timezone set karo (important)
date_default_timezone_set("Asia/Kolkata");

$email = $conn->real_escape_string($_POST['email']);

$res = $conn->query("SELECT * FROM admin_users WHERE email='$email'");
if ($res->num_rows === 0) {
    die("Email not registered.");
}

$otp = rand(100000, 999999);
$expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// ✅ Purane OTP delete karo (same email ke liye)
$conn->query("DELETE FROM password_otps WHERE email='$email'");

// ✅ Naya OTP insert karo
$conn->query("INSERT INTO password_otps (email, otp, expires_at) VALUES ('$email', '$otp', '$expires_at')");

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 0; // Debugging band (production ke liye)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kinjalolakiya60@gmail.com';  // Your Gmail
    $mail->Password   = 'wmyy dzbe jibm byei';        // Your App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('kinjalolakiya60@gmail.com', 'Attendance App');
    $mail->addAddress($email);

    $mail->isHTML(false);
    $mail->Subject = 'OTP for Password Reset';
    $mail->Body    = "Your OTP is: $otp\nIt is valid for 10 minutes.";

    $mail->send();

    // ✅ Redirect to OTP verify page
    header("Location: verify_otp.php?email=" . urlencode($email));
    exit();

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
