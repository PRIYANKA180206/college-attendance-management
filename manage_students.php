<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php'; // should provide $conn (mysqli)

/* ---------- Init ---------- */
$edit_student = null;
$message = "";
$msg_type = ""; // success / error

/* ---------- Load dropdowns into arrays ---------- */
$departments = [];
$semesters = [];
$shifts = [];

$dr = $conn->query("SELECT id, name FROM departments ORDER BY name");
if ($dr) while ($r = $dr->fetch_assoc()) $departments[] = $r;

$sr = $conn->query("SELECT id, number FROM semesters ORDER BY number");
if ($sr) while ($r = $sr->fetch_assoc()) $semesters[] = $r;

$sh = $conn->query("SELECT id, name FROM shifts ORDER BY name");
if ($sh) while ($r = $sh->fetch_assoc()) $shifts[] = $r;

/* ---------- Handle POST: Add Student ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $name = trim($_POST['name'] ?? '');
    $roll_no = trim($_POST['roll_no'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $semester_id = (int)($_POST['semester_id'] ?? 0);
    $shift_id = (int)($_POST['shift_id'] ?? 0);

    if ($name !== '' && $roll_no !== '' && $department_id && $semester_id && $shift_id) {
        // check duplicate roll number
        $check = $conn->prepare("SELECT id FROM students WHERE roll_no=?");
        $check->bind_param("s", $roll_no);
        $check->execute();
        $res = $check->get_result();
        if ($res && $res->num_rows > 0) {
            $message = "❌ Roll Number already exists!";
            $msg_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (name, roll_no, department_id, semester_id, shift_id) VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssiii", $name, $roll_no, $department_id, $semester_id, $shift_id);
            if ($stmt->execute()) {
                $message = "✅ Student added successfully!";
                $msg_type = "success";
            }
            $stmt->close();
        }
        $check->close();
    }
}

/* ---------- Handle POST: Update Student ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $id = (int)($_POST['student_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $roll_no = trim($_POST['roll_no'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $semester_id = (int)($_POST['semester_id'] ?? 0);
    $shift_id = (int)($_POST['shift_id'] ?? 0);

    if ($id && $name !== '' && $roll_no !== '' && $department_id && $semester_id && $shift_id) {
        // check duplicate roll no excluding self
        $check = $conn->prepare("SELECT id FROM students WHERE roll_no=? AND id<>?");
        $check->bind_param("si", $roll_no, $id);
        $check->execute();
        $res = $check->get_result();
        if ($res && $res->num_rows > 0) {
            $message = "❌ Roll Number already exists!";
            $msg_type = "error";
        } else {
            $stmt = $conn->prepare("UPDATE students SET name=?, roll_no=?, department_id=?, semester_id=?, shift_id=? WHERE id=?");
            $stmt->bind_param("ssiiii", $name, $roll_no, $department_id, $semester_id, $shift_id, $id);
            if ($stmt->execute()) {
                $message = "✅ Student updated successfully!";
                $msg_type = "success";
            }
            $stmt->close();
        }
        $check->close();
    }
}

/* ---------- Handle GET: Delete ---------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id) {
        $dstmt = $conn->prepare("DELETE FROM students WHERE id=?");
        $dstmt->bind_param("i", $id);
        $dstmt->execute();
        $dstmt->close();
        $message = "🗑️ Student deleted!";
        $msg_type = "success";
    }
}

/* ---------- Handle GET: Edit (load student) ---------- */
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid) {
        $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
        $stmt->bind_param("i", $eid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows) $edit_student = $res->fetch_assoc();
        $stmt->close();
    }
}

/* ---------- Fetch student list with search ---------- */
$search = trim($_GET['search'] ?? '');
$query = "SELECT s.*, d.name AS dept_name, sem.number AS sem_number, sh.name AS shift_name
          FROM students s
          JOIN departments d ON s.department_id=d.id
          JOIN semesters sem ON s.semester_id=sem.id
          JOIN shifts sh ON s.shift_id=sh.id";

if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $query .= " WHERE s.name LIKE '%$safe%' 
                OR s.roll_no LIKE '%$safe%' 
                OR d.name LIKE '%$safe%' 
                OR sem.number LIKE '%$safe%' 
                OR sh.name LIKE '%$safe%'";
}

$query .= " ORDER BY s.name ASC";
$students = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Manage Students - Student Attendance</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Base styles */
*{box-sizing:border-box}
body{margin:0;font-family:'Segoe UI',Tahoma,sans-serif;background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);color:#e8fbff;height:100vh;overflow:hidden;}
.header{position:fixed;top:0;left:0;right:0;height:64px;background:#071014;color:#00e7ff;display:flex;align-items:center;justify-content:space-between;padding:0 20px;z-index:1000;box-shadow:0 4px 12px rgba(0,0,0,0.45);}
.header .brand{display:flex;align-items:center;gap:12px;font-weight:700;font-size:18px}
.header img{height:36px}
.sidebar{position:fixed;top:64px;left:0;bottom:48px;width:240px;background:#0d1112;padding:18px;overflow-y:auto;border-right:1px solid rgba(255,255,255,0.03);}
.sidebar h2{color:#00d9ff;margin:0 0 12px 0;font-size:20px}
.sidebar a{display:flex;align-items:center;gap:10px;color:#cfeff4;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;}
.sidebar a i{width:22px;text-align:center;color:#7eefff}
.sidebar a:hover{background:rgba(0,217,255,0.06); transform:translateX(6px)}
.footer{position:fixed;left:240px;right:0;bottom:0;height:48px;background:#071014;color:#00d9ff;display:flex;align-items:center;justify-content:center;border-top:1px solid rgba(255,255,255,0.03);}
.main{position:absolute;top:64px;bottom:48px;left:240px;right:0;padding:26px 32px;overflow-y:auto;}
.page-title{font-size:24px;color:#00e7ff;margin:6px 0 18px;display:flex;align-items:center;gap:12px}
.content-grid{display:grid;grid-template-columns:2fr 1fr;gap:26px;align-items:start}
.card{background:rgba(7,9,10,0.5);padding:16px;border-radius:12px;box-shadow:0 8px 22px rgba(3,12,14,0.45);border:1px solid rgba(255,255,255,0.03);}
.table-wrap{overflow:auto; max-height:66vh; border-radius:8px}
table{width:100%; border-collapse:collapse; min-width:680px}
thead th{position:sticky; top:0; background:linear-gradient(90deg,#07a8b4,#00e7ff);color:#042426; padding:12px; text-align:left; font-weight:800;}
tbody tr{background:rgba(0,0,0,0.2); border-bottom:1px solid rgba(255,255,255,0.03)}
tbody td{padding:12px;color:#dff9ff}
tbody tr:hover td{background:rgba(255,255,255,0.01)}
.actions a{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;text-decoration:none;color:#00e7ff;background:rgba(0,217,255,0.04);margin-right:6px;}
.actions a:hover{background:rgba(0,217,255,0.12); color:#022426; transform:translateY(-3px)}
.form-title{font-weight:800;color:#bff9ff;margin-bottom:10px}
.form-row{display:flex;gap:12px;margin-bottom:12px}
.field{display:flex;flex-direction:column;gap:6px;flex:1}
label{font-size:13px;color:#c9f5fb}
input[type="text"], select{padding:12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:#0f1415;color:#e8fbff;font-size:14px;outline:none;}
input:focus, select:focus{box-shadow:0 6px 18px rgba(0,217,255,0.06); border-color:rgba(0,217,255,0.25)}
.btn{display:inline-flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;border:none;cursor:pointer;font-weight:700;font-size:15px}
.btn-primary{background:linear-gradient(90deg,#00f0ff,#00b8d1);color:#002428;box-shadow:0 8px 20px rgba(0,216,255,0.12)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 34px rgba(0,216,255,0.16)}
.btn-ghost{background:transparent;border:1px solid rgba(255,255,255,0.04);color:#bff5fb;padding:10px 14px}
.badge{display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(0,0,0,0.35);color:#9ff6ff;font-weight:700}
.message{padding:10px 14px;border-radius:6px;margin-bottom:12px;font-weight:600;}
.message.success{background:rgba(0,255,170,0.12);color:#00ff9d;border:1px solid rgba(0,255,170,0.3);}
.message.error{background:rgba(255,80,80,0.12);color:#ff7b7b;border:1px solid rgba(255,80,80,0.3);}
@media (max-width:1000px){.content-grid{grid-template-columns:1fr}.sidebar{display:none}.main{left:0}.footer{left:0}}
</style>
</head>
<body>
    <div class="header">
        <div class="brand"><img src="assets/logo-removebg-preview.png" alt="logo"> Student Attendance</div>
        <div class="user"><i class="fas fa-user-shield"></i>&nbsp; Admin</div>
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
        <div class="page-title"><i class="fas fa-user-graduate"></i> Manage Students</div>

        <?php if ($message): ?>
            <div class="message <?= $msg_type ?>"><?= $message ?></div>
            <script>
                setTimeout(()=> {
                    document.querySelector('.message').style.display='none';
                }, 5000);
            </script>
        <?php endif; ?>

        <div class="content-grid">

            <!-- LEFT: Student list -->
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="badge"><i class="fas fa-list"></i></div>
                        <div style="font-weight:800;font-size:18px;color:#bff9ff;">Student List</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <!-- Searchbar -->
                        <form method="get" style="display:flex;align-items:center;gap:6px;">
                            <input type="text" name="search" placeholder="Search student..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                                   style="padding:8px 10px;border-radius:6px;border:1px solid #00d9ff;
                                          background:#111417;color:#e6f9ff;font-size:14px;">
                            <button type="submit" style="padding:8px 12px;border:none;border-radius:6px;
                                                         background:#00d9ff;color:#000;cursor:pointer;">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <div style="color:#9fdff2;font-size:14px;">
                            Total: <?= $students ? $students->num_rows : 0 ?>
                        </div>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:6%">#</th>
                                <th style="width:32%">Name</th>
                                <th style="width:16%">Roll No</th>
                                <th style="width:18%">Department</th>
                                <th style="width:12%">Semester</th>
                                <th style="width:16%">Shift / Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($students && $students->num_rows) {
                            $i = 1;
                            while ($s = $students->fetch_assoc()) {
                                $sid = (int)$s['id'];
                                $sname = htmlspecialchars($s['name']);
                                $roll = htmlspecialchars($s['roll_no']);
                                $dept = htmlspecialchars($s['dept_name']);
                                $sem = htmlspecialchars($s['sem_number']);
                                $shift = htmlspecialchars($s['shift_name']);
                                echo "<tr>
                                        <td style='font-weight:700;'>$i</td>
                                        <td>$sname</td>
                                        <td>$roll</td>
                                        <td>$dept</td>
                                        <td>$sem</td>
                                        <td>
                                            <div style='display:flex;align-items:center;justify-content:flex-end'>
                                                <span style='margin-right:10px;color:#9fdff2;font-weight:700'>$shift</span>
                                                <div class='actions'>
                                                    <a title='Edit' href='?edit=$sid'><i class='fas fa-edit'></i></a>
                                                    <a title='Delete' href='?delete=$sid' onclick=\"return confirm('Delete this student?')\"><i class='fas fa-trash'></i></a>
                                                </div>
                                            </div>
                                        </td>
                                      </tr>";
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;color:#9fdff2'>No students found.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT: Add/Edit student -->
            <div class="card">
                <div class="form-title"><?= $edit_student ? 'Edit Student' : 'Add Student' ?></div>
                <form method="post">
                    <?php if ($edit_student): ?>
                        <input type="hidden" name="student_id" value="<?= (int)$edit_student['id'] ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="field">
                            <label>Name</label>
                            <input type="text" name="name" required value="<?= htmlspecialchars($edit_student['name'] ?? '') ?>">
                        </div>
                        <div class="field">
                            <label>Roll Number</label>
                            <input type="text" name="roll_no" required value="<?= htmlspecialchars($edit_student['roll_no'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label>Department</label>
                            <select name="department_id" required>
                                <option value="">Select</option>
                                <?php foreach($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= ($edit_student && $edit_student['department_id']==$d['id'])?'selected':'' ?>>
                                        <?= htmlspecialchars($d['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Semester</label>
                            <select name="semester_id" required>
                                <option value="">Select</option>
                                <?php foreach($semesters as $sem): ?>
                                    <option value="<?= $sem['id'] ?>" <?= ($edit_student && $edit_student['semester_id']==$sem['id'])?'selected':'' ?>>
                                        <?= htmlspecialchars($sem['number']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Shift</label>
                            <select name="shift_id" required>
                                <option value="">Select</option>
                                <?php foreach($shifts as $sh): ?>
                                    <option value="<?= $sh['id'] ?>" <?= ($edit_student && $edit_student['shift_id']==$sh['id'])?'selected':'' ?>>
                                        <?= htmlspecialchars($sh['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:10px;">
                        <?php if ($edit_student): ?>
                            <button type="submit" name="update_student" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="manage_students.php" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_student" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Student
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="footer">&copy; <?= date("Y") ?> Student Attendance Management</div>
</body>
</html>
