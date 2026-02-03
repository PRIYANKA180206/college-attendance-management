<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $department_id = $_POST['department_id'];
    $semester_id = $_POST['semester_id'];
    $shift_id = $_POST['shift_id'];

    $stmt = $conn->prepare("INSERT INTO students (name, roll_no, department_id, semester_id, shift_id)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $name, $roll_no, $department_id, $semester_id, $shift_id);

    if ($stmt->execute()) {
        header("Location: manage_students.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
