<?php
include 'db_connect.php';

$department_id = (int)($_POST['department_id'] ?? 0);
$semester_id   = (int)($_POST['semester_id'] ?? 0);
$selected      = (int)($_POST['selected'] ?? 0);

if($department_id && $semester_id){
    $subjects = $conn->query("SELECT * FROM subjects WHERE department_id=$department_id AND semester_id=$semester_id");
    echo "<option value=''>-- Subject --</option>";
    while($row = $subjects->fetch_assoc()){
        $sel = ($row['id'] == $selected) ? "selected" : "";
        echo "<option value='{$row['id']}' $sel>{$row['name']}</option>";
    }
} else {
    echo "<option value=''>-- Subject --</option>";
}
?>
