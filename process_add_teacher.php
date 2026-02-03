<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $department_id = $_POST['department_id'];
    $shift_id = $_POST['shift_id'];

    $stmt = $conn->prepare("INSERT INTO teachers (name, username, password, department_id, shift_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $name, $username, $password, $department_id, $shift_id);

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
