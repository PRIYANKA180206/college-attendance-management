<?php
include 'db_connect.php';

$dept  = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
$shift = isset($_POST['shift_id']) ? (int)$_POST['shift_id'] : 0;

if($dept && $shift){
    $result = $conn->query("SELECT id, name FROM teachers WHERE department_id=$dept AND shift_id=$shift ORDER BY name");
    if($result->num_rows > 0){
        echo "<option value=''>-- Teacher --</option>";
        while($row = $result->fetch_assoc()){
            echo "<option value='".$row['id']."'>".$row['name']."</option>";
        }
    } else {
        echo "<option value=''>No teachers found</option>";
    }
} else {
    echo "<option value=''>-- Teacher --</option>";
}
?>
