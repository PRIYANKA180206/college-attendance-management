<?php
include 'db.php'; // Database connection

// Get POST data safely
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$contact = $_POST['contact'];
$email = $_POST['email'];

// Server-side validations
if (!preg_match("/^[A-Za-z]+$/", $username)) {
    die("❌ Invalid username: letters only.");
}

if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=]).{6,}$/", $password)) {
    die("❌ Password must include at least one capital letter, one number, and one symbol.");
}

if ($password !== $confirm_password) {
    die("❌ Passwords do not match.");
}

if (!preg_match("/^\d{10}$/", $contact)) {
    die("❌ Contact must be 10 digits.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("❌ Invalid email format.");
}

// Check if username already exists
$stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("❌ Username already exists.");
}
$stmt->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$stmt = $conn->prepare("INSERT INTO admin_users (username, password, contact, email) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $hashed_password, $contact, $email);

if ($stmt->execute()) {
    header("Location: login.php?msg=registered");
    exit();
} else {
    echo "❌ Registration failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
