<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['student_name'])) {
    header("Location: student_login.php");
    exit();
}

$login_name = $_SESSION['student_name'];

// 1. student details निकालना
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
$student_id = $student['id'];

// 2. subjects dropdown ke liye
$sub_sql = "SELECT id, name FROM subjects WHERE department_id = ? AND semester_id = ?";
$stmt3 = $conn->prepare($sub_sql);
$stmt3->bind_param("ii", $student['department_id'], $student['semester_id']);
$stmt3->execute();
$subjects_res = $stmt3->get_result();

// filters
$filter_subject = isset($_GET['subject_id']) ? $_GET['subject_id'] : '';
$filter_from = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$filter_to = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// 3. Attendance fetch (subject + date range)
$result = null;
if (!empty($filter_subject) && !empty($filter_from) && !empty($filter_to)) {
    $sql = "SELECT a.date, a.status, sub.name AS subject, t.name AS teacher
            FROM attendance a
            JOIN subjects sub ON a.subject_id = sub.id
            JOIN teachers t ON a.teacher_id = t.id
            WHERE a.student_id = ? AND a.subject_id = ? AND a.date BETWEEN ? AND ?
            ORDER BY a.date DESC";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("iiss", $student_id, $filter_subject, $filter_from, $filter_to);
    $stmt2->execute();
    $result = $stmt2->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {margin:0;font-family:'Segoe UI';background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);display:flex;justify-content:center;align-items:center;height:100vh;color:#fff;}
        .page-box{background:#111;padding:30px;border-radius:20px;width:1000px;text-align:center;box-shadow:0 0 30px rgba(0,0,0,0.8);animation:fadeIn 0.8s ease;max-height:90vh;overflow:auto;}
        @keyframes fadeIn{from{opacity:0;transform:scale(0.9);}to{opacity:1;transform:scale(1);}}
        h2{color:#00d9ff;margin-bottom:15px;font-size:26px;}
        .student-info{margin-bottom:15px;font-size:15px;text-align:left;}
        table{width:100%;border-collapse:collapse;background:#1a1a1a;border-radius:10px;overflow:hidden;}
        th,td{padding:10px;border-bottom:1px solid #333;text-align:center;}
        th{background:#00d9ff;color:#000;}
        tr:hover{background:#222;}
        .status-present{color:lime;font-weight:bold;}
        .status-absent{color:red;font-weight:bold;}
        .status-leave{color:orange;font-weight:bold;}
        a.back-btn{display:inline-block;margin-top:20px;padding:10px 20px;background:#00d9ff;color:#000;border-radius:8px;text-decoration:none;font-weight:bold;}
        a.back-btn:hover{background:#00a8cc;}
        .filter-box{margin-bottom:20px;text-align:left;}
        select,input[type=date]{padding:6px 10px;border-radius:6px;border:none;margin-right:10px;}
        button{padding:6px 14px;border:none;border-radius:6px;background:#00d9ff;font-weight:bold;cursor:pointer;}
        button:hover{background:#00a8cc;}
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
        <span><i class="fas fa-user-shield"></i> student</span>
    </div>
<div class="page-box">
    <h2><i class="fas fa-calendar-check"></i> Attendance - <?php echo $student['name']; ?></h2>
    
    <div class="student-info">
        <strong>Name:</strong> <?php echo $student['name']; ?> |
        <strong>Roll No:</strong> <?php echo $student['roll_no']; ?> |
        <strong>Dept:</strong> <?php echo $student['department']; ?> |
        <strong>Sem:</strong> <?php echo $student['semester']; ?> |
        <strong>Shift:</strong> <?php echo $student['shift']; ?>
    </div>

    <!-- Filter Form -->
    <div class="filter-box">
        <form method="GET">
            <label><strong>Subject:</strong></label>
            <select name="subject_id" required>
                <option value="">--Select Subject--</option>
                <?php 
                $subjects_res->data_seek(0);
                while($sub = $subjects_res->fetch_assoc()): ?>
                    <option value="<?php echo $sub['id']; ?>" <?php if($filter_subject == $sub['id']) echo "selected"; ?>>
                        <?php echo $sub['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label><strong>From:</strong></label>
            <input type="date" name="from_date" value="<?php echo $filter_from; ?>" required>

            <label><strong>To:</strong></label>
            <input type="date" name="to_date" value="<?php echo $filter_to; ?>" required>

            <button type="submit">Show Attendance</button>
        </form>
    </div>
    
    <?php if($result): ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Subject</th>
            <th>Teacher</th>
            <th>Status</th>
        </tr>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date("d-M-Y", strtotime($row['date'])); ?></td>
                    <td><?php echo $row['subject']; ?></td>
                    <td><?php echo $row['teacher']; ?></td>
                    <td class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No attendance records found</td></tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>

    <a href="student_dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
</div>
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
</body>
</html>
