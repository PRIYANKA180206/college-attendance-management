<?php
session_start();
include 'db_connect.php'; // Make sure this connects to student_attendance_db

if(!isset($_SESSION['student_name'])) {
    header("Location: student_login.php");
    exit();
}

$login_name = $_SESSION['student_name'];

// Fetch student details
$student_sql = "SELECT s.id, s.name, s.roll_no, 
                       d.name AS department, se.number AS semester, sh.name AS shift
                FROM students s
                LEFT JOIN departments d ON s.department_id = d.id
                LEFT JOIN semesters se ON s.semester_id = se.id
                LEFT JOIN shifts sh ON s.shift_id = sh.id
                WHERE s.name = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("s", $login_name);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("<h2 style='color:red;text-align:center;'>No student found!</h2>");
}
$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
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

.profile-box {
    background:#111;
    padding:40px;
    border-radius:20px;
    width:500px;
    text-align:center;
    box-shadow:0 0 30px rgba(0,0,0,0.8);
    animation:fadeIn 0.8s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:scale(0.9);}
    to{opacity:1;transform:scale(1);}
}

.profile-box h2 {
    color:#00d9ff;
    margin-bottom:25px;
    font-size:28px;
}

.profile-item {
    display:flex;
    align-items:center;
    gap:15px;
    background:#1a1a1a;
    padding:12px 15px;
    border-radius:10px;
    margin-bottom:12px;
    transition:0.3s;
}

.profile-item:hover {
    background:#222;
}

.profile-item i {
    font-size:20px;
    color:#00d9ff;
    width:25px;
}

.back-btn {
    display:inline-block;
    margin-top:20px;
    padding:10px 20px;
    background:#00d9ff;
    color:#000;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
}

.back-btn:hover {
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
        <span><i class="fas fa-user-shield"></i> Student</span>
    </div>
<div class="profile-box">
    <h2><i class="fas fa-user-circle"></i> My Profile</h2>

    <div class="profile-item"><i class="fas fa-id-badge"></i> <strong>Roll No:</strong> <?php echo $student['roll_no']; ?></div>
    <div class="profile-item"><i class="fas fa-user"></i> <strong>Name:</strong> <?php echo $student['name']; ?></div>
    <div class="profile-item"><i class="fas fa-building"></i> <strong>Department:</strong> <?php echo $student['department']; ?></div>
    <div class="profile-item"><i class="fas fa-layer-group"></i> <strong>Semester:</strong> <?php echo $student['semester']; ?></div>
    <div class="profile-item"><i class="fas fa-clock"></i> <strong>Shift:</strong> <?php echo $student['shift']; ?></div>

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
