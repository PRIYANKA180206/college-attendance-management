<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: manage_subjects.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
</head>
<body>
    <h2>Edit Subject</h2>
    <form method="POST" action="update_subject.php">
        <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">

        <label>Subject Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($subject['name']); ?>" required><br>

        <label>Department ID:</label>
        <input type="number" name="department_id" value="<?php echo $subject['department_id']; ?>" required><br>

        <label>Semester ID:</label>
        <input type="number" name="semester_id" value="<?php echo $subject['semester_id']; ?>" required><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
