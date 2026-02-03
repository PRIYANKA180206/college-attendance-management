<?php
session_start();
if(!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}
$student_name = $_SESSION['student_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            color: #fff;
            padding-bottom: 120px; /* space for footer */
        }
        .dashboard-box {
            background: #111;
            padding: 50px;
            border-radius: 20px;
            width: 900px;
            text-align: center;
            box-shadow: 0 0 40px rgba(0,0,0,0.8);
            animation: fadeIn 0.8s ease;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.9);}
            to {opacity: 1; transform: scale(1);}
        }
        h2 {
            color: #00d9ff;
            margin-bottom: 20px;
            font-size: 28px;
        }
        p {
            font-size: 16px;
            margin-bottom: 30px;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
        }
        .card {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 0 20px rgba(0,0,0,0.6);
            transition: 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            display: block;
        }
        .card:hover {
            transform: translateY(-7px);
            background: #222;
        }
        .card i {
            font-size: 32px;
            color: #00d9ff;
            margin-bottom: 12px;
        }
        .card span {
            display: block;
            font-size: 15px;
            font-weight: bold;
        }
        .logout-btn {
            margin-top: 40px;
            padding: 14px 25px;
            background: #00d9ff;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            color: #000;
            font-weight: bold;
            font-size: 16px;
            transition: 0.3s;
        }
        .logout-btn:hover {
            background: #00a8cc;
            transform: scale(1.05);
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
        .header-right {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
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
        <h1><img src="assets/logo.png" alt="App Logo">Student Attendance</h1>
        <div class="header-right">
            <i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($student_name); ?>
        </div>
    </div>

    <div class="dashboard-box">
        <h2><i class="fas fa-user-graduate"></i> Welcome, <?php echo htmlspecialchars($student_name); ?>!</h2>
        <p>Select an option below to continue:</p>

        <div class="card-container">
            <a href="view_attendance.php" class="card">
                <i class="fas fa-calendar-check"></i>
                <span>View Attendance</span>
            </a>
            <a href="subjects.php" class="card">
                <i class="fas fa-book"></i>
                <span>Subjects</span>
            </a>
            <a href="reports.php" class="card">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="profile.php" class="card">
                <i class="fas fa-user-edit"></i>
                <span>Profile</span>
            </a>
            <a href="feedback.php" class="card">
                <i class="fas fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
        </div>

        <form method="POST" action="stud_logout.php">
            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
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
