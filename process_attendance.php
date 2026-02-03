<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
    $teacher_id = isset($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : 0;
    $attendance_date = $_POST['attendance_date'] ?? '';
    $status_data = $_POST['status'] ?? [];

    if (!$subject_id || !$teacher_id || empty($attendance_date) || empty($status_data)) {
        echo "<script>alert('Missing required data.'); window.location.href = 'take_attendance.php';</script>";
        exit();
    }

    // Prepare insert and check queries
    $insert_stmt = $conn->prepare("INSERT INTO attendance (student_id, subject_id, teacher_id, date, status) VALUES (?, ?, ?, ?, ?)");
    $check_stmt = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND subject_id = ? AND date = ?");

    $inserted_count = 0;

    foreach ($status_data as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = $conn->real_escape_string($status);

        $check_stmt->bind_param("iis", $student_id, $subject_id, $attendance_date);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows === 0) {
            $insert_stmt->bind_param("iiiss", $student_id, $subject_id, $teacher_id, $attendance_date, $status);
            $insert_stmt->execute();
            $inserted_count++;
        }
    }

    $check_stmt->close();
    $insert_stmt->close();
    $conn->close();

    echo "<script>alert('$inserted_count student(s) attendance recorded successfully.'); window.location.href = 'take_attendance.php';</script>";
    exit();
} else {
    header("Location: take_attendance.php");
    exit();
}
?>
