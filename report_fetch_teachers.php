<?php
include 'db_connect.php';

$department_id = (int)($_POST['department_id'] ?? 0);
$shift_id      = (int)($_POST['shift_id'] ?? 0);
$selected      = (int)($_POST['selected'] ?? 0);

if($department_id && $shift_id){
    $teachers = $conn->query("SELECT * FROM teachers WHERE department_id=$department_id AND shift_id=$shift_id");
    echo "<option value=''>-- Teacher --</option>";
    while($row = $teachers->fetch_assoc()){
        $sel = ($row['id'] == $selected) ? "selected" : "";
        echo "<option value='{$row['id']}' $sel>{$row['name']}</option>";
    }
} else {
    echo "<option value=''>-- Teacher --</option>";
}
?>
