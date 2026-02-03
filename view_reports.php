<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Load filters
$departments = $conn->query("SELECT * FROM departments");
$semesters   = $conn->query("SELECT * FROM semesters");
$shifts      = $conn->query("SELECT * FROM shifts");

$attendance = false;
$where = "1";
$filters = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['department_id'])) {
    if (!empty($_POST['department_id'])) {
        $filters['department_id'] = (int)$_POST['department_id'];
        $where .= " AND s.department_id = " . $filters['department_id'];
    }
    if (!empty($_POST['semester_id'])) {
        $filters['semester_id'] = (int)$_POST['semester_id'];
        $where .= " AND s.semester_id = " . $filters['semester_id'];
    }
    if (!empty($_POST['shift_id'])) {
        $filters['shift_id'] = (int)$_POST['shift_id'];
        $where .= " AND s.shift_id = " . $filters['shift_id'];
    }
    if (!empty($_POST['subject_id'])) {
        $filters['subject_id'] = (int)$_POST['subject_id'];
        $where .= " AND a.subject_id = " . $filters['subject_id'];
    }
    if (!empty($_POST['teacher_id'])) {
        $filters['teacher_id'] = (int)$_POST['teacher_id'];
        $where .= " AND a.teacher_id = " . $filters['teacher_id'];
    }
    if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
        $filters['from_date'] = $_POST['from_date'];
        $filters['to_date']   = $_POST['to_date'];
        $where .= " AND a.date BETWEEN '" . $filters['from_date'] . "' AND '" . $filters['to_date'] . "'";
    }

    $sql = "SELECT a.date, a.status, s.name AS student_name, s.roll_no, 
                   d.name AS dept_name, sem.number AS semester, sh.name AS shift, 
                   sub.name AS subject, t.name AS teacher, s.id as student_id
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN subjects sub ON a.subject_id = sub.id
            JOIN teachers t ON a.teacher_id = t.id
            JOIN departments d ON s.department_id = d.id
            JOIN semesters sem ON s.semester_id = sem.id
            JOIN shifts sh ON s.shift_id = sh.id
            WHERE $where
            ORDER BY a.date ASC";

    $attendance = $conn->query($sql);

    $pivot = [];
    $dates = [];

    if ($attendance && $attendance->num_rows > 0) {
        while ($row = $attendance->fetch_assoc()) {
            $pivot[$row['student_id']]['name'] = $row['student_name'];
            $pivot[$row['student_id']]['roll_no'] = $row['roll_no'];
            $pivot[$row['student_id']]['dept_name'] = $row['dept_name'];
            $pivot[$row['student_id']]['semester'] = $row['semester'];
            $pivot[$row['student_id']]['shift'] = $row['shift'];
            $pivot[$row['student_id']]['subject'] = $row['subject'];
            $pivot[$row['student_id']]['teacher'] = $row['teacher'];
            $pivot[$row['student_id']]['attendance'][$row['date']] = $row['status'];

            if (!in_array($row['date'], $dates)) {
                $dates[] = $row['date'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Reports</title>
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
select,input[type="date"],input[type="text"]{
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
    color:#042426; padding:12px; text-align:center; font-weight:800
}
tbody tr{background:rgba(0,0,0,0.2); border-bottom:1px solid rgba(255,255,255,0.03)}
tbody td{padding:10px;color:#dff9ff;text-align:center}
tbody tr:hover td{background:rgba(255,255,255,0.02)}

/* Status colors */
.present{background:#2ecc71; color:#000; font-weight:bold}
.absent{background:#e74c3c; color:#fff; font-weight:bold}
.leave{background:#f1c40f; color:#000; font-weight:bold}
.empty{background:#555; color:#fff}

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
    <div class="page-title"><i class="fas fa-chart-line"></i> Attendance Report</div>

    <!-- Filters -->
    <div class="card">
        <form method="POST" class="filters">
            <select name="department_id" id="department">
                <option value="">-- Department --</option>
                <?php 
                $departments->data_seek(0);
                while($d=$departments->fetch_assoc()): ?>
                    <option value="<?= $d['id'] ?>" <?= (isset($filters['department_id']) && $filters['department_id']==$d['id'])?'selected':'' ?>><?= $d['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <select name="semester_id" id="semester">
                <option value="">-- Semester --</option>
                <?php 
                $semesters->data_seek(0);
                while($s=$semesters->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>" <?= (isset($filters['semester_id']) && $filters['semester_id']==$s['id'])?'selected':'' ?>>Semester <?= $s['number'] ?></option>
                <?php endwhile; ?>
            </select>

            <select name="shift_id" id="shift">
                <option value="">-- Shift --</option>
                <?php 
                $shifts->data_seek(0);
                while($sh=$shifts->fetch_assoc()): ?>
                    <option value="<?= $sh['id'] ?>" <?= (isset($filters['shift_id']) && $filters['shift_id']==$sh['id'])?'selected':'' ?>><?= $sh['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <!-- Dynamic Subject -->
            <select name="subject_id" id="subject" data-selected="<?= $filters['subject_id'] ?? '' ?>">
                <option value="">-- Subject --</option>
            </select>

            <!-- Dynamic Teacher -->
            <select name="teacher_id" id="teacher" data-selected="<?= $filters['teacher_id'] ?? '' ?>">
                <option value="">-- Teacher --</option>
            </select>

            <input type="date" name="from_date" value="<?= $filters['from_date'] ?? '' ?>">
            <input type="date" name="to_date" value="<?= $filters['to_date'] ?? '' ?>">

            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>

    <!-- Search -->
    <input type="text" id="searchInput" placeholder="Search student..." style="padding:10px;border-radius:8px;margin-bottom:15px;width:250px;border:none">

    <!-- Report Table -->
    <div class="card">
    <?php if ($attendance !== false): ?>
        <?php if (!empty($pivot)): ?>
            <p><b>Department:</b> <?= reset($pivot)['dept_name'] ?> | 
               <b>Semester:</b> <?= reset($pivot)['semester'] ?> | 
               <b>Shift:</b> <?= reset($pivot)['shift'] ?> | 
               <b>Subject:</b> <?= reset($pivot)['subject'] ?> | 
               <b>Teacher:</b> <?= reset($pivot)['teacher'] ?>
            </p>

            <form method="POST" action="export_excel.php">
                <?php foreach($_POST as $k=>$v): ?>
                    <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-excel"></i> Export to Excel</button>
            </form>

            <div class="table-wrap">
                <table id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Roll No</th>
                            <?php foreach ($dates as $d): ?>
                                <th><?= date("d-m", strtotime($d)) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pivot as $stud): ?>
                            <tr>
                                <td><?= htmlspecialchars($stud['name']) ?></td>
                                <td><?= htmlspecialchars($stud['roll_no']) ?></td>
                                <?php foreach ($dates as $d): 
                                    $status = $stud['attendance'][$d] ?? '';
                                    $class = $status=='Present'?'present':($status=='Absent'?'absent':($status=='Leave'?'leave':'empty'));
                                ?>
                                    <td class="<?= $class ?>"><?= $status ?: '-' ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No records found.</p>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>

<div class="footer">&copy; <?= date("Y") ?>  Student Attendance App | Developed by Kinjal & Priyanka</div>

<script>
$(document).ready(function(){
    function loadSubjects() {
        var dept = $("#department").val();
        var sem  = $("#semester").val();
        var selected = $("#subject").data("selected"); // selected value

        if(dept && sem){
            $.post("report_fetch_subjects.php",{department_id:dept, semester_id:sem},function(data){
                $("#subject").html(data);
                if(selected){ $("#subject").val(selected); }
            });
        } else {
            $("#subject").html("<option value=''>-- Subject --</option>");
        }
    }

    function loadTeachers(){
        var dept  = $("#department").val();
        var shift = $("#shift").val();
        var selected = $("#teacher").data("selected"); // selected value

        if(dept && shift){
            $.post("report_fetch_teachers.php",{department_id:dept, shift_id:shift},function(data){
                $("#teacher").html(data);
                if(selected){ $("#teacher").val(selected); }
            });
        } else {
            $("#teacher").html("<option value=''>-- Teacher --</option>");
        }
    }

    $("#department, #semester").change(loadSubjects);
    $("#department, #shift").change(loadTeachers);

    // Initial load to maintain selection after page reload
    loadSubjects();
    loadTeachers();

    // Search filter
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#attendanceTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
</body>
</html>
