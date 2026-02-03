<?php
session_start();
include 'db_connect.php'; // <-- apka database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll_no = trim($_POST['roll_no']);

    if (empty($roll_no)) {
        $_SESSION['error_msg'] = "Roll number is required!";
        header("Location: login.php");
        exit;
    }

    // Check roll number in DB
    $stmt = $conn->prepare("SELECT id, name, roll_no FROM students WHERE roll_no = ?");
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Roll number does not exist
        $_SESSION['error_msg'] = "Invalid Roll Number!";
        header("Location: student_login.php");
        exit;
    }

    $student = $result->fetch_assoc();

    if (!$student) {
        // Student not found
        $_SESSION['error_msg'] = "Student does not exist!";
        header("Location: student_login.php");
        exit;
    }

    // Success: Set session
    $_SESSION['student_id'] = $student['id'];
    $_SESSION['student_name'] = $student['name'];
    $_SESSION['roll_no'] = $student['roll_no'];

    // Redirect to dashboard
    header("Location: student_dashboard.php");
    exit;
}
