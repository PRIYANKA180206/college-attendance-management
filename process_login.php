<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db.php'; // Database connection

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Successful login
            $_SESSION['admin_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Invalid password
            $error_msg = urlencode("❌ Invalid password.");
            header("Location: login.php?error=$error_msg&username=" . urlencode($username));
            exit();
        }
    } else {
        // No such user
        $error_msg = urlencode("❌ No such user found.");
        header("Location: login.php?error=$error_msg");
        exit();
    }

    $stmt->close();
    $conn->close();
} 
