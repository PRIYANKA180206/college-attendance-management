<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['student_name'])) {
    header("Location: student_login.php");
    exit();
}

$login_name = $_SESSION['student_name'];

// 1. Student details
$student_sql = "SELECT s.id, s.name, s.roll_no, s.department_id, s.semester_id, 
                       d.name AS department, se.number AS semester, sh.name AS shift 
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

// 2. Subjects for dropdown
$sub_sql = "SELECT id, name FROM subjects WHERE department_id = ? AND semester_id = ?";
$stmt2 = $conn->prepare($sub_sql);
$stmt2->bind_param("ii", $student['department_id'], $student['semester_id']);
$stmt2->execute();
$subjects_res = $stmt2->get_result();

// 3. Attendance fetch (based on filter)
$attendance_records = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'] ?? '';
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';

    $query = "SELECT a.date, sub.name AS subject_name, a.status 
              FROM attendance a
              INNER JOIN subjects sub ON a.subject_id = sub.id
              INNER JOIN students st ON a.student_id = st.id
              WHERE st.name = ?";

    $params = [$login_name];
    $types = "s";

    if (!empty($subject_id)) {
        $query .= " AND a.subject_id = ?";
        $params[] = $subject_id;
        $types .= "i";
    }
    if (!empty($from_date) && !empty($to_date)) {
        $query .= " AND a.date BETWEEN ? AND ?";
        $params[] = $from_date;
        $params[] = $to_date;
        $types .= "ss";
    }

    $query .= " ORDER BY a.date ASC";
    $stmt3 = $conn->prepare($query);
    $stmt3->bind_param($types, ...$params);
    $stmt3->execute();
    $attendance_records = $stmt3->get_result();

    // Handle CSV Export
    if(isset($_POST['export_csv'])) {
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=attendance_report_{$student['name']}.csv");
        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, ['Date', 'Subject', 'Status']);

        // Data rows
        if($attendance_records->num_rows > 0){
            while($row = $attendance_records->fetch_assoc()){
                fputcsv($output, [$row['date'], $row['subject_name'], $row['status']]);
            }
        }

        fclose($output);
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {margin:0;font-family:'Segoe UI';background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);display:flex;justify-content:center;align-items:center;height:100vh;color:#fff;}
        .page-box{background:#111;padding:30px;border-radius:20px;width:1000px;text-align:center;box-shadow:0 0 30px rgba(0,0,0,0.8);animation:fadeIn 0.8s ease;max-height:90vh;overflow:auto;}
        @keyframes fadeIn{from{opacity:0;transform:scale(0.9);}to{opacity:1;transform:scale(1);}}
        h2{color:#00d9ff;margin-bottom:15px;font-size:26px;}
        .student-info{margin-bottom:15px;font-size:15px;text-align:left;}
        form{margin-bottom:20px;display:flex;gap:15px;justify-content:center;flex-wrap:wrap;}
        select,input{padding:8px;border-radius:6px;border:none;}
        button{padding:10px 20px;background:#00d9ff;border:none;border-radius:8px;color:#000;font-weight:bold;cursor:pointer;}
        button:hover{background:#00a8cc;}
        table{width:100%;border-collapse:collapse;background:#1a1a1a;border-radius:10px;overflow:hidden;}
        th,td{padding:12px;border-bottom:1px solid #333;text-align:center;}
        th{background:#00d9ff;color:#000;}
        tr:hover{background:#222;}
        a.back-btn{display:inline-block;margin-top:20px;padding:10px 20px;background:#00d9ff;color:#000;border-radius:8px;text-decoration:none;font-weight:bold;}
        a.back-btn:hover{background:#00a8cc;}
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
        <span><i class="fas fa-user-shield"></i> Student</span>
    </div>
<div class="page-box">
    <h2><i class="fas fa-file-alt"></i> My Attendance Reports - <?php echo $student['name']; ?></h2>

    <div class="student-info">
        <strong>Department:</strong> <?php echo $student['department']; ?> |
        <strong>Semester:</strong> <?php echo $student['semester']; ?> |
        <strong>Shift:</strong> <?php echo $student['shift']; ?>
    </div>

    <!-- Filter Form -->
    <form method="POST">
        <select name="subject_id">
            <option value="">-- Select Subject --</option>
            <?php while($sub = $subjects_res->fetch_assoc()): ?>
                <option value="<?php echo $sub['id']; ?>" <?php if(isset($subject_id) && $subject_id==$sub['id']) echo 'selected'; ?>>
                    <?php echo $sub['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="date" name="from_date" value="<?php echo $from_date ?? ''; ?>">
        <input type="date" name="to_date" value="<?php echo $to_date ?? ''; ?>">
        <button type="submit" name="filter"><i class="fas fa-search"></i> Filter</button>
        <button type="submit" name="export_csv"><i class="fas fa-file-csv"></i> Export to CSV</button>
    </form>

    <!-- Attendance Table -->
    <table>
        <tr>
            <th>Date</th>
            <th>Subject</th>
            <th>Status</th>
        </tr>
        <?php if(!empty($attendance_records) && $attendance_records->num_rows > 0): ?>
            <?php
            if(isset($attendance_records)) $attendance_records->data_seek(0);
            while($row = $attendance_records->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No records found</td></tr>
        <?php endif; ?>
    </table>

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
