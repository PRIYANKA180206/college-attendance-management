<?php 
session_start(); 
if (!isset($_SESSION['admin_id'])) {     
    header("Location: login.php");     
    exit(); 
} 
include 'db_connect.php'; // make sure this defines $conn (mysqli)

/* --- Initialize --- */
$edit = null;

/* --- Load departments & shifts into arrays (for select options) --- */
$deptOptions = [];
$shiftOptions = [];

$dr = $conn->query("SELECT id, name FROM departments ORDER BY name");
if ($dr) while ($r = $dr->fetch_assoc()) $deptOptions[] = $r;

$sr = $conn->query("SELECT id, name FROM shifts ORDER BY name");
if ($sr) while ($r = $sr->fetch_assoc()) $shiftOptions[] = $r;

/* --- POST: Add teacher --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher'])) {
    $name = trim($_POST['name'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $shift_id = (int)($_POST['shift_id'] ?? 0);

    if ($name !== '' && $department_id && $shift_id) {
        $stmt = $conn->prepare("INSERT INTO teachers (name, department_id, shift_id) VALUES (?,?,?)");
        $stmt->bind_param("sii", $name, $department_id, $shift_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_teachers.php");
    exit();
}

/* --- POST: Update teacher --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_teacher'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $shift_id = (int)($_POST['shift_id'] ?? 0);

    if ($id && $name !== '' && $department_id && $shift_id) {
        $stmt = $conn->prepare("UPDATE teachers SET name=?, department_id=?, shift_id=? WHERE id=?");
        $stmt->bind_param("siii", $name, $department_id, $shift_id, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_teachers.php");
    exit();
}

/* --- GET: Delete teacher --- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id) {
        $dstmt = $conn->prepare("DELETE FROM teachers WHERE id=?");
        $dstmt->bind_param("i", $id);
        $dstmt->execute();
        $dstmt->close();
    }
    header("Location: manage_teachers.php");
    exit();
}

/* --- GET: Edit mode (load teacher) --- */
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid) {
        $stmt = $conn->prepare("SELECT * FROM teachers WHERE id=?");
        $stmt->bind_param("i", $eid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows) $edit = $res->fetch_assoc();
        $stmt->close();
    }
}

/* --- Teacher list with search filter --- */
$search = trim($_GET['search'] ?? '');
$query = "SELECT t.*, d.name AS dept_name, s.name AS shift_name 
          FROM teachers t
          JOIN departments d ON t.department_id=d.id
          JOIN shifts s ON t.shift_id=s.id";
if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $query .= " WHERE t.name LIKE '%$safe%' OR d.name LIKE '%$safe%' OR s.name LIKE '%$safe%'";
}
$query .= " ORDER BY t.name ASC";
$teachers = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Teachers - Student Attendance</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Reset & base */
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color: #e9f6fb;
    height: 100vh;
    overflow: hidden;
}

/* Header */
.header {
    background: #071014;
    color: #00e7ff;
    padding: 12px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    box-shadow: 0 3px 12px rgba(0,0,0,0.5);
}
.header .brand { display:flex; align-items:center; gap:12px; font-weight:700; font-size:18px; }
.header img { height:36px; width:auto; display:block; }
.header .user { opacity:0.9; }

/* Sidebar */
.sidebar {
    width: 240px;
    background: #0f1213;
    position: fixed;
    top: 64px;
    bottom: 48px;
    left: 0;
    padding: 20px 16px;
    overflow-y: auto;
    border-right: 1px solid rgba(255,255,255,0.03);
}
.sidebar h2 { color:#00d9ff; margin:0 0 12px 0; font-size:22px; }
.sidebar a {
    display:flex;
    gap:10px;
    align-items:center;
    color:#cfd8db;
    text-decoration:none;
    padding:10px 12px;
    margin-bottom:6px;
    border-radius:8px;
    transition: all .15s ease;
}
.sidebar a i { width:22px; text-align:center; color:#79e6ff; }
.sidebar a:hover { background: rgba(0,217,255,0.08); color:#fff; transform: translateX(4px); }

/* Footer */
.footer {
    position: fixed;
    bottom: 0;
    left: 240px;
    right: 0;
    height: 48px;
    background: #071014;
    color: #00d9ff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:13px;
    border-top: 1px solid rgba(255,255,255,0.03);
}

/* Main area */
.main {
    position: absolute;
    left: 240px;
    right: 0;
    top: 64px;
    bottom: 48px;
    padding: 28px 36px;
    overflow-y: auto;
}
.page-title { color:#00e7ff; font-size:26px; margin:6px 0 22px 0; display:flex; align-items:center; gap:12px; }

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 28px;
    align-items: start;
}

/* Card */
.card {
    background: rgba(7,7,7,0.45);
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 6px 18px rgba(2,10,12,0.45);
    border: 1px solid rgba(255,255,255,0.03);
}

/* Form */
.form-title { color:#00e7ff; font-weight:700; margin-bottom:12px; font-size:18px; }
.form-row { display:flex; gap:12px; margin-bottom:12px; }
.form-row .field { flex:1; display:flex; flex-direction:column; gap:8px; }
label { font-size:13px; color:#bfeff8; }
input[type="text"], select {
    padding: 12px 14px;
    background:#111417;
    color:#e6f9ff;
    border-radius:8px;
    border: 1px solid rgba(255,255,255,0.04);
    font-size:14px;
}
input[type="text"]:focus, select:focus { box-shadow: 0 0 0 4px rgba(0,217,255,0.06); border-color: rgba(0,217,255,0.25); }

/* Buttons */
.btn {
    display: inline-flex;
    align-items:center;
    gap:10px;
    padding: 12px 16px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
}
.btn-primary {
    background: linear-gradient(90deg,#00f0ff,#00b8d1);
    color: #002428;
}
.btn-primary:hover { transform: translateY(-2px); }
.btn-ghost { background: transparent; color: #bfeff8; border: 1px solid rgba(255,255,255,0.04); }
.form-actions { display:flex; gap:12px; margin-top:6px; }

/* Table */
.table-wrap { overflow: auto; max-height: 62vh; border-radius:8px; }
table { width:100%; border-collapse: collapse; min-width:720px; }
thead th {
    position: sticky; top: 0;
    background: linear-gradient(90deg,#07a8b4,#00e7ff);
    color: #042426; padding: 12px 14px;
    text-align: left; font-weight:800;
}
tbody tr { background: rgba(7,7,7,0.45); border-bottom: 1px solid rgba(255,255,255,0.03); }
tbody td { padding: 14px 12px; color:#dbeff2; }
.actions a {
    display:inline-flex; align-items:center; justify-content:center;
    width:36px; height:36px; border-radius:8px; text-decoration:none;
    color:#00e7ff; background: rgba(0,217,255,0.04);
    margin-right:6px;
}
.actions a:hover { background: rgba(0,217,255,0.12); color:#002428; }
.badge { display:inline-block; padding:6px 10px; border-radius:999px; font-weight:700; font-size:13px; background: rgba(0,0,0,0.35); color:#9ff6ff; }

@media (max-width: 1000px) {
    .content-grid { grid-template-columns: 1fr; }
    .sidebar { display: none; }
    .footer { left: 0; }
    .main { left: 0; }
}
</style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <img src="assets/logo-removebg-preview.png" alt="logo">
            <div>Student Attendance</div>
        </div>
        <div class="user"><i class="fas fa-user-shield"></i> Admin</div>
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
        <div class="page-title"><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</div>

        <div class="content-grid">

            <!-- LEFT: Teacher List -->
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="badge"><i class="fas fa-list"></i></div>
                        <div style="font-weight:800;font-size:18px;color:#bff9ff;">Teacher List</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <!-- Searchbar -->
                        <form method="get" style="display:flex;align-items:center;gap:6px;">
                            <input type="text" name="search" placeholder="Search teacher..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                                   style="padding:8px 10px;border-radius:6px;border:1px solid #00d9ff;
                                          background:#111417;color:#e6f9ff;font-size:14px;">
                            <button type="submit" style="padding:8px 12px;border:none;border-radius:6px;
                                                         background:#00d9ff;color:#000;cursor:pointer;">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <div style="color:#9fdff2;font-size:14px;">
                            Total: <?= $teachers ? $teachers->num_rows : 0 ?>
                        </div>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Shift</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($teachers && $teachers->num_rows) {
                                $i = 1;
                                while ($t = $teachers->fetch_assoc()) {
                                    $tid = (int)$t['id'];
                                    $tname = htmlspecialchars($t['name']);
                                    $dept = htmlspecialchars($t['dept_name']);
                                    $shift = htmlspecialchars($t['shift_name']);
                                    echo "<tr>
                                            <td style='font-weight:700;'>$i</td>
                                            <td>$tname</td>
                                            <td>$dept</td>
                                            <td>$shift</td>
                                            <td class='actions'>
                                                <a title='Edit' href='?edit=$tid'><i class='fas fa-edit'></i></a>
                                                <a title='Delete' href='?delete=$tid' onclick=\"return confirm('Delete this teacher?')\"><i class='fas fa-trash'></i></a>
                                            </td>
                                          </tr>";
                                    $i++;
                                }
                            } else {
                                echo "<tr><td colspan='5' style='padding:18px;text-align:center;color:#9fdff2;'>No teachers found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT: Add / Edit Teacher Form -->
            <div class="card">
                <div class="form-title"><i class="fas fa-user-plus"></i> <?= $edit ? 'Edit Teacher' : 'Add Teacher' ?></div>

                <form method="post" autocomplete="off">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="field">
                            <label for="name">Teacher Name</label>
                            <input id="name" type="text" name="name" placeholder="Enter teacher name" required value="<?= $edit ? htmlspecialchars($edit['name']) : '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="department_id">Department</label>
                            <select id="department_id" name="department_id" required>
                                <option value="">-- Select Department --</option>
                                <?php foreach ($deptOptions as $d): ?>
                                    <option value="<?= (int)$d['id'] ?>" <?= $edit && $edit['department_id']==$d['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label for="shift_id">Shift</label>
                            <select id="shift_id" name="shift_id" required>
                                <option value="">-- Select Shift --</option>
                                <?php foreach ($shiftOptions as $s): ?>
                                    <option value="<?= (int)$s['id'] ?>" <?= $edit && $edit['shift_id']==$s['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <?php if ($edit): ?>
                            <button type="submit" name="update_teacher" class="btn btn-primary"><i class="fas fa-pen"></i> Update Teacher</button>
                            <a href="manage_teachers.php" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_teacher" class="btn btn-primary"><i class="fas fa-plus"></i> Add Teacher</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="footer">
        &copy; <?= date("Y") ?> Student Attendance App | Developed by Kinjal & Priyanka
    </div>
</body>
</html>
