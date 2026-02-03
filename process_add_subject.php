<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $department_id = (int)$_POST['department_id'];
    $semester_id = (int)$_POST['semester_id'];

    if (!empty($name) && $department_id && $semester_id) {
        $stmt = $conn->prepare("INSERT INTO subjects (name, department_id, semester_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $name, $department_id, $semester_id);

        if ($stmt->execute()) {
            echo "<script>alert('Subject added successfully!'); window.location='manage_subjects.php';</script>";
        } else {
            echo "<script>alert('Error: Could not add subject.'); window.location='manage_subjects.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill all fields.'); window.location='manage_subjects.php';</script>";
    }
}
$conn->close();
?>
