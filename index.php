<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome - Student Attendance App</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- FontAwesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
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
    .header h1 {
      font-size: 22px;
      display: flex;
      align-items: center;
      margin: 0;
    }
    .header img {
      height: 40px;
      margin-right: 10px;
    }

    /* MAIN CONTENT */
    .welcome {
      margin-top: 100px; /* header ke niche space */
      text-align: center;
    }
    .welcome img {
      height: 110px;
      margin-right: 10px;
    }

    .welcome h2 {
      font-size: 46px;
      margin-bottom: 40px;
      color: #00d9ff;
      animation: fadeInDown 1s ease;
    }

    .container {
      display: flex;
      gap: 60px;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
    }

    .card {
      background: rgba(0, 0, 0, 0.92);
      border-radius: 20px;
      width: 340px;   /* size increase */
      padding: 40px;  /* padding increase */
      text-align: center;
      box-shadow: 0 0 25px rgba(0, 255, 255, 0.25);
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      animation: fadeInUp 1s ease;
      cursor: pointer;
    }

    .card:hover {
      transform: translateY(-10px) scale(1.05);
      box-shadow: 0 0 40px rgba(0, 217, 255, 0.6);
    }

    .card i {
      font-size: 50px;
      color: #00d9ff;
      margin-bottom: 20px;
    }

    .card h2 {
      font-size: 24px;
      margin-bottom: 15px;
      color: #fff;
    }

    .card p {
      font-size: 15px;
      margin-bottom: 25px;
      color: #ccc;
    }

    .card a {
      display: inline-block;
      padding: 12px 20px;
      border-radius: 8px;
      background: #00d9ff;
      color: black;
      font-weight: bold;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .card a:hover {
      background: #00a8cc;
    }

    /* Animations */
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 600px) {
      .container {
        flex-direction: column;
        gap: 30px;
      }
      .card {
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <div class="header">
    <h1>
      <img src="assets/logo-removebg-preview.png" alt="App Logo"> 
      Student Attendance App
    </h1>
  </div>

  <!-- MAIN CONTENT -->
  <div class="welcome">
    <img src="assets/logo-removebg-preview.png" alt="App Logo"> 
    <h2>Welcome to Student Attendance App</h2>

    <div class="container">
      <!-- Admin / Teacher Login -->
      <div class="card">
        <i class="fas fa-user-shield"></i>
        <h2>Admin / Teacher</h2>
        <p>Login here to manage attendance, students, and reports.</p>
        <a href="login.php">Login</a>
      </div>

      <!-- Student / Client Login -->
      <div class="card">
        <i class="fas fa-user-graduate"></i>
        <h2>Student / Client</h2>
        <p>Login here to check attendance, view profile, and give feedback.</p>
        <a href="client/student_login.php">Login</a>
      </div>
    </div>
  </div>
</body>
</html>
