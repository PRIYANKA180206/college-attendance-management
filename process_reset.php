<?php
session_start();
require 'db.php';

if (!isset($_SESSION['reset_email'])) {
    echo "Session expired.";
    exit;
}

$new_pass = $_POST['new_pass'];
$confirm_pass = $_POST['confirm_pass'];

if ($new_pass !== $confirm_pass) {
    echo "Passwords do not match.";
    exit;
}

$hashed = password_hash($new_pass, PASSWORD_DEFAULT);
$email = $_SESSION['reset_email'];

$sql = "UPDATE admin_users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed, $email);
$stmt->execute();

session_destroy();

header("Location: login.php?msg=reset_success");
?>
