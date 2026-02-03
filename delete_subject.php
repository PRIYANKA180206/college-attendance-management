<?php
include 'db_connect.php';
$id = $_GET['id'];
$conn->query("DELETE FROM subjects WHERE id = $id");
header("Location: manage_subjects.php");
?>
