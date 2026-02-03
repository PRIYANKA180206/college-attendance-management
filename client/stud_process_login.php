<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = trim($_POST['student_name']);
    $roll_no = trim($_POST['roll_no']);

    // Student check in database
    $sql = "SELECT * FROM students WHERE name = ? AND roll_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_name, $roll_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();

        // Set session
        $_SESSION['student_id']  = $student['id'];
        $_SESSION['student_name'] = $student['name'];
        $_SESSION['roll_no']      = $student['roll_no'];

        header("Location: student_dashboard.php");
        exit();
    } else {
        // error msg session में डालो और वापस login पर
        $_SESSION['error_msg'] = "❌ Invalid Name or Roll Number!";
        header("Location: student_login.php");
        exit();
    }
}
