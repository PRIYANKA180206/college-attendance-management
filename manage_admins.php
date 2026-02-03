<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connection.php';

/* CSRF token */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* Add Admin */
if (isset($_POST['add_admin'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf'] ?? '')) {
        die('Invalid CSRF token');
    }

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone    = trim($_POST['phone']);

    if ($username !== "" && $email !== "" && $phone !== "") {
        $stmt = $conn->prepare("INSERT INTO admins (username,email,password,phone) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $username, $email, $password, $phone);
        $stmt->execute();
    }
    header("Location: manage_admins.php");
    exit();
}

/* Update Admin */
if (isset($_POST['update_admin'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf'] ?? '')) {
        die('Invalid CSRF token');
    }

    $id       = (int)$_POST['id'];
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE admins SET username=?, email=?, password=?, phone=? WHERE id=? LIMIT 1");
        $stmt->bind_param("ssssi", $username, $email, $password, $phone, $id);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET username=?, email=?, phone=? WHERE id=? LIMIT 1");
        $stmt->bind_param("sssi", $username, $email, $phone, $id);
    }
    $stmt->execute();
    header("Location: manage_admins.php");
    exit();
}

/* Delete Admin */
if (isset($_POST['delete_admin'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf'] ?? '')) {
        die('Invalid CSRF token');
    }

    $id = (int)($_POST['delete_id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM admins WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: manage_admins.php");
    exit();
}

/* Edit Mode */
$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM admins WHERE id=$eid");
    if ($res && $res->num_rows) $edit = $res->fetch_assoc();
}

/* List */
$admins = $conn->query("SELECT * FROM admins ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Admins</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body{
  margin:0;
  font-family:'Segoe UI',sans-serif;
  background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
  color:#fff;
  display:flex;
}
button {
  margin-top: 15px;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  background-color: #00d9ff !important; /* force background */
  color: #000 !important;              /* force text color */
  font-weight: bold;
  font-size: 14px;
  cursor: pointer;
  display: inline-block; /* ensure visible */
}

button:hover {
  background-color: #00a8cc !important;
  color: #fff !important;
}

.sidebar{
  width:220px;background:#111;padding:20px;
}
.sidebar a{
  color:#ccc;text-decoration:none;display:block;
  padding:10px;border-radius:6px;
}
.sidebar a:hover{background:#00d9ff;color:#000;}
.main{flex:1;padding:30px;animation:fadeIn .6s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0)}}

h2{color:#00d9ff; margin-bottom:15px;font-size:20px;}
form.card{
  background:#1a1a1a;
  padding:18px;
  border-radius:8px;
  margin-bottom:25px;
  max-width:700px;
}
.form-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:15px;
}
.form-grid{
  display: grid;
  grid-template-columns: 1fr 1fr; /* 2 column layout */
  gap: 20px; /* dono ke beech me space */
  margin-bottom: 15px; /* form ke andar neeche bhi space */
}

input{
  width: 100%;
  padding: 10px 12px;
  background: #333;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box; /* important so width adjust ho */
}

button:hover{background:#00a8cc;}

table{
  width:100%;
  border-collapse:collapse;
  background:#222;
  margin-top:15px;
  font-size:14px;
}
th,td{
  padding:10px;
  border:1px solid #444;
  text-align:left;
}
th{background:#00d9ff;color:#000;}
tr:hover{background:#2f2f2f;}
.actions form{display:inline;}
.link-btn{
  background:none;border:none;padding:0;cursor:pointer;
  color:#00d9ff;font-size:15px;
}
.link-btn:hover{color:red;}
#searchInput{
  margin:10px 0;
  padding:8px;
  width:100%;
  max-width:300px;
  border:none;
  border-radius:6px;
  background:#333;
  color:#fff;
  font-size:14px;
}
/* HEADER */
        .header {
            background: #111;
            color: #00d9ff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .header img {
            height: 40px;
            margin-right: 10px;
        }
        .header h1 {
            font-size: 20px;
            display: flex;
            align-items: center;
            margin: 0;
        }

</style>
</head>
<body>
   <div class="header">
        
        <h1> <img src="assets/logo-removebg-preview.png" alt="App Logo"> Student Attendance</h1>
        <span><i class="fas fa-user-shield"></i> Admin</span>
    </div>
<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</a>
  <a href="manage_students.php"><i class="fas fa-user-graduate"></i> Manage Students</a>
  <a href="manage_subjects.php"><i class="fas fa-book"></i> Subjects</a>
  <a href="take_attendance.php"><i class="fas fa-clipboard-check"></i> Attendance</a>
  <a href="view_reports.php"><i class="fas fa-chart-line"></i> Reports</a>
  <a href="manage_admins.php"><i class="fas fa-user-shield"></i> Manage Admins</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
  <h2><i class="fas fa-user-shield"></i> <?= $edit ? 'Edit Admin' : 'Add Admin' ?></h2>
  <form method="post" class="card">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <?php if($edit): ?>
      <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <?php endif; ?>
    <div class="form-grid">
      <input type="text" name="username" placeholder="Username" required value="<?= $edit ? htmlspecialchars($edit['username']) : '' ?>">
      <input type="email" name="email" placeholder="Email" required value="<?= $edit ? htmlspecialchars($edit['email']) : '' ?>">
      <input type="password" name="password" placeholder="<?= $edit ? 'New Password (optional)' : 'Password' ?>">
      <input type="text" name="phone" placeholder="Phone Number" required value="<?= $edit ? htmlspecialchars($edit['phone']) : '' ?>">
    </div>
   <button type="submit" name="<?= $edit ? 'update_admin':'add_admin' ?>">
  <?= $edit ? 'Update Admin':'Add Admin' ?>
</button>

  </form>

  <h2><i class="fas fa-list"></i> Admin List</h2>
  <input type="text" id="searchInput" placeholder="Search by username, email or phone...">

  <table id="adminTable">
    <thead>
      <tr><th>Username</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if($admins && $admins->num_rows): ?>
      <?php while($a=$admins->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($a['username']) ?></td>
          <td><?= htmlspecialchars($a['email']) ?></td>
          <td><?= htmlspecialchars($a['phone']) ?></td>
          <td class="actions">
            <a href="?edit=<?= (int)$a['id'] ?>" title="Edit"><i class="fas fa-edit"></i></a>
            <form method="post" onsubmit="return confirm('Delete this admin?');">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="delete_id" value="<?= (int)$a['id'] ?>">
              <button type="submit" name="delete_admin" class="link-btn" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="4">No admin found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
// Search filter
document.getElementById("searchInput").addEventListener("keyup", function(){
  let filter = this.value.toLowerCase();
  let rows = document.querySelectorAll("#adminTable tbody tr");
  rows.forEach(row=>{
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  });
});
</script>
</body>
</html>
