<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['student_name'])) {
    header("Location: student_login.php");
    exit();
}

$login_name = $_SESSION['student_name'];

// 1. Student details निकालना
$student_sql = "SELECT s.id, s.name, s.roll_no, s.department_id, s.semester_id, d.name AS department, se.number AS semester, sh.name AS shift 
                FROM students s
                LEFT JOIN departments d ON s.department_id = d.id
                LEFT JOIN semesters se ON s.semester_id = se.id
                LEFT JOIN shifts sh ON s.shift_id = sh.id
                WHERE s.name = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("s", $login_name);
$stmt->execute();
$student_result = $stmt->get_result();

if($student_result->num_rows == 0){
    die("<h2 style='color:red;text-align:center;'>No student found with name: $login_name</h2>");
}
$student = $student_result->fetch_assoc();

// 2. Subjects fetch करना (department_id + semester_id से)
$sub_sql = "SELECT id, name FROM subjects WHERE department_id = ? AND semester_id = ?";
$stmt2 = $conn->prepare($sub_sql);
$stmt2->bind_param("ii", $student['department_id'], $student['semester_id']);
$stmt2->execute();
$subjects_res = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Subjects</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin:0;
            font-family:'Segoe UI';
            background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            color:#fff;
        }
        .page-box {
            background:#111;
            padding:30px;
            border-radius:20px;
            width:900px;
            text-align:center;
            box-shadow:0 0 30px rgba(0,0,0,0.8);
            animation:fadeIn 0.8s ease;
            max-height:90vh;
            overflow:auto;
        }
        @keyframes fadeIn {
            from {opacity:0;transform:scale(0.9);}
            to {opacity:1;transform:scale(1);}
        }
        h2 {
            color:#00d9ff;
            margin-bottom:15px;
            font-size:26px;
        }
        .student-info {
            margin-bottom:15px;
            font-size:15px;
            text-align:left;
        }
        table {
            width:100%;
            border-collapse:collapse;
            background:#1a1a1a;
            border-radius:10px;
            overflow:hidden;
        }
        th,td {
            padding:12px;
            border-bottom:1px solid #333;
            text-align:center;
        }
        th {
            background:#00d9ff;
            color:#000;
        }
        tr:hover {
            background:#222;
        }
        a.back-btn {
            display:inline-block;
            margin-top:20px;
            padding:10px 20px;
            background:#00d9ff;
            color:#000;
            border-radius:8px;
            text-decoration:none;
            font-weight:bold;
        }
        a.back-btn:hover {
            background:#00a8cc;
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
/* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #111;
            color: #aaa;
            text-align: center;
            padding: 12px 0 6px;
            font-size: 14px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
        }
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 6px;
        }
        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 16px;
            color: #fff;
            transition: 0.3s;
        }
        .social-icons a.facebook { background: #3b5998; }
        .social-icons a.google { background: #db4437; }
        .social-icons a.twitter { background: #1da1f2; }
        .social-icons a.github { background: #333; }
        .social-icons a:hover { opacity: 0.8; }
    </style>
</head>
<body>
     <div class="header">
        
        <h1> <img src="assets/logo.png" alt="App Logo"> Student Attendance</h1>
        <span><i class="fas fa-user-shield"></i>Student</span>
    </div>
<div class="page-box">
    <h2><i class="fas fa-book"></i> My Subjects - <?php echo $student['name']; ?></h2>
    
    <div class="student-info">
        <strong>Department:</strong> <?php echo $student['department']; ?> |
        <strong>Semester:</strong> <?php echo $student['semester']; ?> |
        <strong>Shift:</strong> <?php echo $student['shift']; ?>
    </div>
    
    <table>
        <tr>
            <th>Subject ID</th>
            <th>Subject Name</th>
        </tr>
        <?php if($subjects_res->num_rows > 0): ?>
            <?php while($sub = $subjects_res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $sub['id']; ?></td>
                    <td><?php echo $sub['name']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">No subjects found</td></tr>
        <?php endif; ?>
    </table>

    <a href="student_dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
</div>
</body>
 <!-- FOOTER -->
    <div class="footer">
        <div class="social-icons">
            <a href="https://www.facebook.com" target="_blank" class="facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.google.com" target="_blank" class="google"><i class="fab fa-google"></i></a>
            <a href="https://twitter.com" target="_blank" class="twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://github.com" target="_blank" class="github"><i class="fab fa-github"></i></a>
        </div>
        © 2025 Student Attendance App | Developed by Kinjal & Priyanka
    </div>
</html>
