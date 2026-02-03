<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php'; // $conn available

/* ---------- Init ---------- */
$edit_subject = null;
$message = "";
$message_type = ""; // success | error

/* ---------- Dropdowns ---------- */
$departments = [];
$semesters = [];

$dr = $conn->query("SELECT id, name FROM departments ORDER BY name");
if ($dr) while ($r = $dr->fetch_assoc()) $departments[] = $r;

$sr = $conn->query("SELECT id, number FROM semesters ORDER BY number");
if ($sr) while ($r = $sr->fetch_assoc()) $semesters[] = $r;

/* ---------- Handle POST: Add ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $name = trim($_POST['name'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $semester_id = (int)($_POST['semester_id'] ?? 0);

    if ($name !== '' && $department_id && $semester_id) {
        // Duplicate check
        $check = $conn->prepare("SELECT id FROM subjects WHERE name=? AND department_id=? AND semester_id=?");
        $check->bind_param("sii", $name, $department_id, $semester_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "⚠️ Subject already exists in this Department & Semester!";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO subjects (name, department_id, semester_id) VALUES (?,?,?)");
            $stmt->bind_param("sii", $name, $department_id, $semester_id);
            $stmt->execute();
            $stmt->close();
            $message = "✅ Subject added successfully!";
            $message_type = "success";
        }
        $check->close();
    }
}

/* ---------- Handle POST: Update ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subject'])) {
    $id = (int)($_POST['subject_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $semester_id = (int)($_POST['semester_id'] ?? 0);

    if ($id && $name !== '' && $department_id && $semester_id) {
        // Duplicate check (excluding current subject)
        $check = $conn->prepare("SELECT id FROM subjects WHERE name=? AND department_id=? AND semester_id=? AND id<>?");
        $check->bind_param("siii", $name, $department_id, $semester_id, $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "⚠️ Subject already exists in this Department & Semester!";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("UPDATE subjects SET name=?, department_id=?, semester_id=? WHERE id=?");
            $stmt->bind_param("siii", $name, $department_id, $semester_id, $id);
            $stmt->execute();
            $stmt->close();
            $message = "✅ Subject updated successfully!";
            $message_type = "success";
        }
        $check->close();
    }
}

/* ---------- Handle GET: Delete ---------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id) {
        $dstmt = $conn->prepare("DELETE FROM subjects WHERE id=?");
        $dstmt->bind_param("i", $id);
        $dstmt->execute();
        $dstmt->close();
    }
    header("Location: manage_subjects.php");
    exit();
}

/* ---------- Handle GET: Edit ---------- */
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid) {
        $stmt = $conn->prepare("SELECT * FROM subjects WHERE id=?");
        $stmt->bind_param("i", $eid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows) $edit_subject = $res->fetch_assoc();
        $stmt->close();
    }
}

/* ---------- Fetch subjects ---------- */
$subjects = $conn->query("SELECT s.*, d.name AS dept_name, sem.number AS sem_number
    FROM subjects s
    JOIN departments d ON s.department_id=d.id
    JOIN semesters sem ON s.semester_id=sem.id
    ORDER BY d.name, sem.number, s.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Manage Subjects - Student Attendance</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* === SAME DESIGN AS STUDENTS PAGE === */
*{box-sizing:border-box}
body{margin:0;font-family:'Segoe UI',Tahoma,sans-serif;background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);color:#e8fbff;height:100vh;overflow:hidden}
.header{position:fixed;top:0;left:0;right:0;height:64px;background:#071014;color:#00e7ff;display:flex;align-items:center;justify-content:space-between;padding:0 20px;z-index:1000;box-shadow:0 4px 12px rgba(0,0,0,0.45)}
.header .brand{display:flex;align-items:center;gap:12px;font-weight:700;font-size:18px}
.header img{height:36px}
.sidebar{position:fixed;top:64px;left:0;bottom:48px;width:240px;background:#0d1112;padding:18px;overflow-y:auto;border-right:1px solid rgba(255,255,255,0.03)}
.sidebar h2{color:#00d9ff;margin:0 0 12px 0;font-size:20px}
.sidebar a{display:flex;align-items:center;gap:10px;color:#cfeff4;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px}
.sidebar a i{width:22px;text-align:center;color:#7eefff}
.sidebar a:hover{background:rgba(0,217,255,0.06);transform:translateX(6px)}
.footer{position:fixed;left:240px;right:0;bottom:0;height:48px;background:#071014;color:#00d9ff;display:flex;align-items:center;justify-content:center;border-top:1px solid rgba(255,255,255,0.03)}
.main{position:absolute;top:64px;bottom:48px;left:240px;right:0;padding:26px 32px;overflow-y:auto}
.page-title{font-size:24px;color:#00e7ff;margin:6px 0 18px;display:flex;align-items:center;gap:12px}
.content-grid{display:grid;grid-template-columns:2fr 1fr;gap:26px;align-items:start}
.card{background:rgba(7,9,10,0.5);padding:16px;border-radius:12px;box-shadow:0 8px 22px rgba(3,12,14,0.45);border:1px solid rgba(255,255,255,0.03)}
.table-wrap{overflow:auto;max-height:66vh;border-radius:8px}
table{width:100%;border-collapse:collapse;min-width:600px}
thead th{position:sticky;top:0;background:linear-gradient(90deg,#07a8b4,#00e7ff);color:#042426;padding:12px;text-align:left;font-weight:800}
tbody tr{background:rgba(0,0,0,0.2);border-bottom:1px solid rgba(255,255,255,0.03)}
tbody td{padding:12px;color:#dff9ff}
tbody tr:hover td{background:rgba(255,255,255,0.01)}
.actions a{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;text-decoration:none;color:#00e7ff;background:rgba(0,217,255,0.04);margin-right:6px}
.actions a:hover{background:rgba(0,217,255,0.12);color:#022426;transform:translateY(-3px)}
.form-title{font-weight:800;color:#bff9ff;margin-bottom:10px}
.form-row{display:flex;gap:12px;margin-bottom:12px}
.field{display:flex;flex-direction:column;gap:6px;flex:1}
label{font-size:13px;color:#c9f5fb}
input[type="text"],select{padding:12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:#0f1415;color:#e8fbff;font-size:14px;outline:none}
input:focus,select:focus{box-shadow:0 6px 18px rgba(0,217,255,0.06);border-color:rgba(0,217,255,0.25)}
.btn{display:inline-flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;border:none;cursor:pointer;font-weight:700;font-size:15px}
.btn-primary{background:linear-gradient(90deg,#00f0ff,#00b8d1);color:#002428;box-shadow:0 8px 20px rgba(0,216,255,0.12)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 34px rgba(0,216,255,0.16)}
.btn-ghost{background:transparent;border:1px solid rgba(255,255,255,0.04);color:#bff5fb;padding:10px 14px}
.search-box{margin-bottom:12px}
.search-box input{width:100%;padding:12px;border-radius:8px;background:#0f1415;border:1px solid rgba(255,255,255,0.04);color:#e8fbff}
.message{padding:12px 18px;margin-bottom:14px;border-radius:8px;font-weight:600;text-align:center}
.message.success{background:rgba(0,255,120,0.15);color:#2aff9a;border:1px solid #2aff9a}
.message.error{background:rgba(255,0,60,0.15);color:#ff4d6d;border:1px solid #ff4d6d}
@media(max-width:1000px){.content-grid{grid-template-columns:1fr}.sidebar{display:none}.main{left:0}.footer{left:0}}
</style>
<script>
function filterSubjects(){
    let q=document.getElementById("search").value.toLowerCase();
    document.querySelectorAll("#subjects-body tr").forEach(tr=>{
        let text=tr.innerText.toLowerCase();
        tr.style.display=text.includes(q)?"":"none";
    });
}
</script>
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
        <div class="page-title"><i class="fas fa-book"></i> Manage Subjects</div>

        <?php if ($message): ?>
            <div class="message <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
            <script>
                setTimeout(()=>{document.querySelector('.message').style.display='none';},5000);
            </script>
        <?php endif; ?>

        <div class="content-grid">

            <!-- LEFT: Subject list -->
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div class="badge"><i class="fas fa-list"></i></div>
                        <div style="font-weight:800;font-size:18px;color:#bff9ff">Subject List</div>
                    </div>
                    <div style="color:#9fdff2;font-size:14px">Total: <?= $subjects ? $subjects->num_rows : 0 ?></div>
                </div>

                <!-- Search -->
                <div class="search-box">
                    <input type="text" id="search" placeholder="Search subjects..." onkeyup="filterSubjects()">
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:40%">Subject</th>
                                <th style="width:25%">Department</th>
                                <th style="width:20%">Semester</th>
                                <th style="width:15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subjects-body">
                        <?php
                        if ($subjects && $subjects->num_rows) {
                            while ($s = $subjects->fetch_assoc()) {
                                $sid=(int)$s['id'];
                                $sname=htmlspecialchars($s['name']);
                                $dept=htmlspecialchars($s['dept_name']);
                                $sem=htmlspecialchars($s['sem_number']);
                                echo "<tr>
                                    <td>$sname</td>
                                    <td>$dept</td>
                                    <td>Semester $sem</td>
                                    <td class='actions'>
                                        <a title='Edit' href='?edit=$sid'><i class='fas fa-edit'></i></a>
                                        <a title='Delete' href='?delete=$sid' onclick=\"return confirm('Delete this subject?')\"><i class='fas fa-trash'></i></a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='padding:18px;text-align:center;color:#9fdff2'>No subjects found.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT: Add/Edit form -->
            <div class="card">
                <div class="form-title"><i class="fas fa-plus"></i> <?= $edit_subject ? 'Edit Subject' : 'Add Subject' ?></div>
                <form method="post" autocomplete="off">
                    <?php if ($edit_subject): ?>
                        <input type="hidden" name="subject_id" value="<?= (int)$edit_subject['id'] ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="field">
                            <label for="name">Subject Name</label>
                            <input id="name" type="text" name="name" placeholder="Enter subject name" required value="<?= $edit_subject ? htmlspecialchars($edit_subject['name']) : '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="department_id">Department</label>
                            <select id="department_id" name="department_id" required>
                                <option value="">-- Select Department --</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= $edit_subject && $edit_subject['department_id']==$d['id']?'selected':'' ?>>
                                        <?= htmlspecialchars($d['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label for="semester_id">Semester</label>
                            <select id="semester_id" name="semester_id" required>
                                <option value="">-- Select Semester --</option>
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?= $sem['id'] ?>" <?= $edit_subject && $edit_subject['semester_id']==$sem['id']?'selected':'' ?>>
                                        Semester <?= htmlspecialchars($sem['number']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:8px;display:flex;gap:12px;align-items:center">
                        <?php if ($edit_subject): ?>
                            <button type="submit" name="update_subject" class="btn btn-primary"><i class="fas fa-pen"></i> Update</button>
                            <a href="manage_subjects.php" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_subject" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div class="footer">&copy; <?= date("Y") ?>   Student Attendance App | Developed by Kinjal & Priyanka</div>
</body>
</html>
