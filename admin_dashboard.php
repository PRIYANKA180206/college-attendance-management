<?php 
session_start(); 
if (!isset($_SESSION['admin_id'])) {     
    header("Location: login.php");     
    exit(); 
} 

// DATABASE CONNECTION
$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$dbname = "student_attendance_db";   // <-- your DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch total students
$studentCount = 0;
$sql = "SELECT COUNT(*) AS total FROM students";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $studentCount = $row['total'];
}

// Fetch total teachers
$teacherCount = 0;
$sql = "SELECT COUNT(*) AS total FROM teachers";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $teacherCount = $row['total'];
}

// Fetch total subjects
$subjectCount = 0;
$sql = "SELECT COUNT(*) AS total FROM subjects";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $subjectCount = $row['total'];
}

// Fetch today's attendance percentage
$attendancePercent = 0;
$today = date("Y-m-d");

$sqlTotal = "SELECT COUNT(*) AS total FROM students";
$resTotal = $conn->query($sqlTotal);
$totalStudentsToday = ($resTotal && $row = $resTotal->fetch_assoc()) ? $row['total'] : 0;

$sqlPresent = "SELECT COUNT(DISTINCT student_id) AS present 
               FROM attendance 
               WHERE date='$today' AND status='Present'";
$resPresent = $conn->query($sqlPresent);
$presentCount = ($resPresent && $row = $resPresent->fetch_assoc()) ? $row['present'] : 0;

if ($totalStudentsToday > 0) {
    $attendancePercent = round(($presentCount / $totalStudentsToday) * 100, 2);
}
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Student Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #fff;
            display: flex;
            flex-direction: column;
            height: 100vh;
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

        /* LAYOUT */
        .content-wrapper {
            display: flex;
            flex-grow: 1;
            margin-top: 60px; /* header height */
        }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
            background-color: #111;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            position: fixed;
            top: 60px; /* below header */
            bottom: 40px; /* above footer */
            overflow-y: auto;
        }

        .sidebar a {
            color: #ccc;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 6px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #00d9ff;
            color: #000;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 220px;
            padding: 30px;
            flex-grow: 1;
            overflow-y: auto;
            animation: fadeIn 0.7s ease;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #00d9ff;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
            background: rgba(0,217,255,0.2);
        }

        .card i {
            font-size: 30px;
            margin-bottom: 10px;
            color: #00d9ff;
        }

        /* FOOTER */
        .footer {
            background: #111;
            color: #ccc;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Social Links in Footer */
    .social-login {
      margin-top: 6px;
    }

    .social-icons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 8px;
    }

    .social-icons a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
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

    <!-- HEADER -->
    <div class="header">
        
        <h1> <img src="assets/logo-removebg-preview.png" alt="App Logo"> Student Attendance</h1>
        <span><i class="fas fa-user-shield"></i> Admin</span>
    </div>

    <div class="content-wrapper">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</a>
            <a href="manage_students.php"><i class="fas fa-user-graduate"></i> Manage Students</a>
            <a href="manage_subjects.php"><i class="fas fa-book"></i> Subjects</a>
            <a href="take_attendance.php"><i class="fas fa-clipboard-check"></i> Attendance</a>
            <a href="view_reports.php"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Select an option from the sidebar to manage the system.</p>

            <!-- Dynamic Stats Cards -->
            <div class="cards">
                <div class="card">
                    <i class="fas fa-users"></i>
                    <h3>Total Students</h3>
                    <p><?php echo $studentCount; ?></p>
                </div>
                <div class="card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Total Teachers</h3>
                    <p><?php echo $teacherCount; ?></p>
                </div>
                <div class="card">
                    <i class="fas fa-book-open"></i>
                    <h3>Subjects</h3>
                    <p><?php echo $subjectCount; ?></p>
                </div>
                <div class="card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Attendance Today</h3>
                    <p><?php echo $attendancePercent; ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">

  <div class="social-login">
    <div class="social-icons">
      <a href="https://www.facebook.com" target="_blank" class="facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="https://www.google.com" target="_blank" class="google"><i class="fab fa-google"></i></a>
      <a href="https://twitter.com" target="_blank" class="twitter"><i class="fab fa-twitter"></i></a>
      <a href="https://github.com" target="_blank" class="github"><i class="fab fa-github"></i></a>
    </div>
      © 2025 Student Attendance App | Developed by Kinjal & Priyanka
  </div>
</div>


</body>
</html>
