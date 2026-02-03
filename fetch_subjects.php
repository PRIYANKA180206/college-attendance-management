<?php
include 'db_connect.php';

$dept = (int)$_POST['department_id'];
$sem = (int)$_POST['semester_id'];

$result = $conn->query("SELECT * FROM subjects WHERE department_id=$dept AND semester_id=$sem");

$options = "<option value=''>-- Subject --</option>";
while($row = $result->fetch_assoc()){
    $options .= "<option value='".$row['id']."'>".$row['name']."</option>";
}
echo $options;
