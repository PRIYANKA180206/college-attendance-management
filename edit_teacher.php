<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $department_id = $_POST['department_id'];
    $shift_id = $_POST['shift_id'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE teachers SET name=?, username=?, password=?, department_id=?, shift_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiii", $name, $username, $password, $department_id, $shift_id, $id);
    } else {
        $sql = "UPDATE teachers SET name=?, username=?, department_id=?, shift_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $name, $username, $department_id, $shift_id, $id);
    }

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        echo "Error updating record.";
    }
}
?>
