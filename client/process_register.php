<?php
include 'db_connect.php'; // DB Connection

if(isset($_POST['register'])) {
    $student_name = trim($_POST['student_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if(empty($student_name) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required'); window.location='student_register.php';</script>";
        exit();
    }

    if($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match'); window.location='student_register.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data
    $stmt = $conn->prepare("INSERT INTO stud_user (student_name, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $student_name, $hashed_password);

    if($stmt->execute()) {
        echo "<script>alert('Registration Successful! Please Login'); window.location='login.php';</script>";
    } else {
        if(strpos($stmt->error, 'Duplicate') !== false){
            echo "<script>alert('Student name already exists!'); window.location='student_register.php';</script>";
        } else {
            echo "<script>alert('Error: ".$stmt->error."'); window.location='student_register.php';</script>";
        }
    }
    $stmt->close();
}
?>
