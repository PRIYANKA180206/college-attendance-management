<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM teachers WHERE id = $id");
}

header("Location: manage_teachers.php");
exit();
?>
