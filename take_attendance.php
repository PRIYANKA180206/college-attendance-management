<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$departments = $conn->query("SELECT * FROM departments");
$semesters   = $conn->query("SELECT * FROM semesters");
$shifts      = $conn->query("SELECT * FROM shifts");

$students = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['load_students'])) {
    $dept  = (int)$_POST['department_id'];
    $sem   = (int)$_POST['semester_id'];
    $shift = (int)$_POST['shift_id'];

    $students = $conn->query("SELECT * FROM students WHERE department_id=$dept AND semester_id=$sem AND shift_id=$shift");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Take Attendance - Student Attendance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);color:#e8fbff;height:100vh;overflow:hidden}

/* Header */
.header{
    position:fixed; top:0; left:0; right:0;
    height:64px; background:#071014; color:#00e7ff;
    display:flex; align-items:center; justify-content:space-between;
    padding:0 20px; z-index:1000;
    box-shadow:0 4px 12px rgba(0,0,0,0.45);
}
.header img{height:36px}
.header .brand{display:flex; align-items:center; gap:12px; font-weight:700; font-size:18px}

/* Sidebar */
.sidebar{
    position:fixed; top:64px; left:0; bottom:48px;
    width:240px; background:#0d1112; padding:18px;
    overflow-y:auto; border-right:1px solid rgba(255,255,255,0.03)
}
.sidebar h2{color:#00d9ff; margin:0 0 12px 0; font-size:20px}
.sidebar a{
    display:flex; align-items:center; gap:10px;
    color:#cfeff4; text-decoration:none;
    padding:10px; border-radius:8px; margin-bottom:6px
}
.sidebar a i{width:22px; text-align:center; color:#7eefff}
.sidebar a:hover{background:rgba(0,217,255,0.06); transform:translateX(6px)}

/* Footer */
.footer{
    position:fixed; left:240px; right:0; bottom:0;
    height:48px; background:#071014; color:#00d9ff;
    display:flex; align-items:center; justify-content:center;
    border-top:1px solid rgba(255,255,255,0.03)
}

/* Main */
.main{
    position:absolute; top:64px; bottom:48px; left:240px; right:0;
    padding:26px 32px; overflow-y:auto
}
.page-title{font-size:24px;color:#00e7ff;margin:6px 0 18px;display:flex;align-items:center;gap:12px}

/* Card */
.card{
    background:rgba(7,9,10,0.5); padding:16px; border-radius:12px;
    box-shadow:0 8px 22px rgba(3,12,14,0.45);
    border:1px solid rgba(255,255,255,0.03); margin-bottom:26px
}

/* Filters form */
.filters{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px}
select,input[type="date"]{
    padding:12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);
    background:#0f1415;color:#e8fbff;font-size:14px;outline:none
}
select:focus,input:focus{box-shadow:0 6px 18px rgba(0,217,255,0.06); border-color:rgba(0,217,255,0.25)}

/* Table */
.table-wrap{overflow:auto; border-radius:8px}
table{width:100%; border-collapse:collapse; min-width:600px}
thead th{
    position:sticky; top:0;
    background:linear-gradient(90deg,#07a8b4,#00e7ff);
    color:#042426; padding:12px; text-align:left; font-weight:800
}
tbody tr{background:rgba(0,0,0,0.2); border-bottom:1px solid rgba(255,255,255,0.03)}
tbody td{padding:12px;color:#dff9ff}
tbody tr:hover td{background:rgba(255,255,255,0.02)}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;border:none;cursor:pointer;font-weight:700;font-size:15px}
.btn-primary{background:linear-gradient(90deg,#00f0ff,#00b8d1);color:#002428;box-shadow:0 8px 20px rgba(0,216,255,0.12)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 34px rgba(0,216,255,0.16)}

/* Responsive */
@media(max-width:1000px){
    .sidebar{display:none}
    .main{left:0}
    .footer{left:0}
}
</style>
</head>
<body>
<div class="header">
    <div class="brand"><img src="assets/logo-removebg-preview.png" alt="logo"> Student Attendance</div>
    <div><i class="fas fa-user-shield"></i>&nbsp; Admin</div>
</div>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</a>
    <a href="manage_students.php"><i class="fas fa-user-graduate"></i> Manage Students</a>
    <a href="manage_subjects.php"><i class="fas fa-book"></i> Subjects</a>
    <a href="take_attendance.php"><i class="fas fa-clipboard-check"></i> Attendance</a>
    <a href="view_reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <div class="page-title"><i class="fas fa-clipboard-check"></i> Take Attendance</div>

    <!-- Filters -->
    <div class="card">
        <form method="POST" class="filters">
            <select name="department_id" id="department" required>
                <option value="">-- Department --</option>
                <?php while ($row = $departments->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <select name="semester_id" id="semester" required>
                <option value="">-- Semester --</option>
                <?php while ($row = $semesters->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>">Semester <?= $row['number'] ?></option>
                <?php endwhile; ?>
            </select>

            <select name="shift_id" id="shift" required>
                <option value="">-- Shift --</option>
                <?php while ($row = $shifts->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <select name="subject_id" id="subject" required>
                <option value="">-- Subject --</option>
            </select>

            <select name="teacher_id" id="teacher" required>
                <option value="">-- Teacher --</option>
            </select>

            <input type="date" name="attendance_date" required>

            <button type="submit" name="load_students" class="btn btn-primary"><i class="fas fa-search"></i> Load Students</button>
        </form>
    </div>

    <!-- Student List -->
    <?php if (!empty($students) && $students->num_rows > 0): ?>
    <div class="card">
        <form action="process_attendance.php" method="POST">
            <input type="hidden" name="subject_id" value="<?= $_POST['subject_id'] ?>">
            <input type="hidden" name="teacher_id" value="<?= $_POST['teacher_id'] ?>">
            <input type="hidden" name="attendance_date" value="<?= $_POST['attendance_date'] ?>">

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['roll_no']) ?></td>
                            <td>
                                <label><input type="radio" name="status[<?= $student['id'] ?>]" value="Present" checked required> Present</label>
                                <label><input type="radio" name="status[<?= $student['id'] ?>]" value="Absent"> Absent</label>
                                <label><input type="radio" name="status[<?= $student['id'] ?>]" value="Leave"> Leave</label>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <br>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit Attendance</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<div class="footer">&copy; <?= date("Y") ?>   Student Attendance App | Developed by Kinjal & Priyanka</div>

<script>
$(document).ready(function(){
    function loadSubjects(){
        var dept = $("#department").val();
        var sem  = $("#semester").val();
        if(dept && sem){
            $.post("fetch_subjects.php",{department_id:dept,semester_id:sem},function(data){
                $("#subject").html(data);
            });
        } else {
            $("#subject").html("<option value=''>-- Subject --</option>");
        }
    }
    function loadTeachers(){
        var dept  = $("#department").val();
        var shift = $("#shift").val();
        if(dept && shift){
            $.post("fetch_teachers.php",{department_id:dept,shift_id:shift},function(data){
                $("#teacher").html(data);
            });
        } else {
            $("#teacher").html("<option value=''>-- Teacher --</option>");
        }
    }
    $("#department, #semester").change(loadSubjects);
    $("#department, #shift").change(loadTeachers);
});
</script>
</body>
</html>
