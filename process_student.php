<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Handle Add
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $department_id = $_POST['department_id'];
    $semester_id = $_POST['semester_id'];
    $shift_id = $_POST['shift_id'];

    $stmt = $conn->prepare("INSERT INTO students (name, roll_no, department_id, semester_id, shift_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $name, $roll_no, $department_id, $semester_id, $shift_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_students.php?success=1");
    exit();
}

// Handle Update
if (isset($_POST['update_student'])) {
    $id = $_POST['student_id'];
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $department_id = $_POST['department_id'];
    $semester_id = $_POST['semester_id'];
    $shift_id = $_POST['shift_id'];

    $stmt = $conn->prepare("UPDATE students SET name=?, roll_no=?, department_id=?, semester_id=?, shift_id=? WHERE id=?");
    $stmt->bind_param("ssiiii", $name, $roll_no, $department_id, $semester_id, $shift_id, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_students.php?updated=1");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id");
    header("Location: manage_students.php?deleted=1");
    exit();
}
?>
