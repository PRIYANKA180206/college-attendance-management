<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $department_id = (int)$_POST['department_id'];
    $semester_id = (int)$_POST['semester_id'];

    if ($id && !empty($name) && $department_id && $semester_id) {
        $stmt = $conn->prepare("UPDATE subjects SET name=?, department_id=?, semester_id=? WHERE id=?");
        $stmt->bind_param("siii", $name, $department_id, $semester_id, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Subject updated successfully!'); window.location='manage_subjects.php';</script>";
        } else {
            echo "<script>alert('Error: Could not update subject.'); window.location='manage_subjects.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill all fields.'); window.location='manage_subjects.php';</script>";
    }
}
$conn->close();
?>
