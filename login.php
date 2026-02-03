<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Attendance App - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
      padding-top: 70px;  
      padding-bottom: 100px; 
      box-sizing: border-box;
    }

    .login-box {
      background: rgba(0, 0, 0, 0.95);
      padding: 35px;
      border-radius: 18px;
      width: 95%;
      max-width: 400px;
      box-shadow: 0 0 30px rgba(0, 255, 255, 0.25);
      animation: fadeIn 0.6s ease;
      text-align: center;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    img {
      width: 110px;
      margin-bottom: 15px;
    }

    h2 {
      margin-bottom: 20px;
      color: #00d9ff;
      font-size: 26px;
    }

    .error-message {
      background-color: #ff4c4c;
      color: #fff;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
      animation: fadeIn 0.6s ease;
    }

    .input-box {
      position: relative;
      margin-bottom: 20px;
    }

    .input-box input {
      width: 100%;
      padding: 12px 15px 12px 45px; 
      border: none;
      border-radius: 10px;
      background: #e6f5ff;
      color: black;
      font-size: 15px;
      box-sizing: border-box;
    }

    .input-box i {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
    }

    .input-box .icon-left {
      left: 15px;
      color: #00d9ff;
    }

    .input-box .toggle-password {
      right: 15px;
      color: #666;
      cursor: pointer;
    }

    .input-box input:focus {
      outline: 2px solid #00d9ff;
      background: #fff;
    }

    .login-btn {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: none;
      border-radius: 10px;
      background-color: #00d9ff;
      color: black;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-btn:hover {
      background-color: #00a8cc;
    }

    .register-link {
      margin-top: 14px;
      display: block;
      color: #ccc;
      font-size: 13px;
      text-decoration: none;
    }

    .register-link:hover {
      color: #00d9ff;
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
    .header-left {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .header-left img {
      height: 45px;
    }
    .header-left h1 {
      font-size: 20px;
      margin: 0;
    }
    .header-right {
      display: flex;
      align-items: center;
      gap: 6px;
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
    <div class="header-left">
      <img src="assets/logo-removebg-preview.png" alt="App Logo">
      <h1>Student Attendance</h1>
    </div>
    <div class="header-right">
      <i class="fas fa-user-shield"></i> Admin
    </div>
  </div>

  <!-- LOGIN BOX -->
  <div class="login-box">
    <img src="assets/logo-removebg-preview.png" alt="App Logo">
    <h2>Student Attendance App</h2>

    <!-- ERROR MESSAGE -->
    <?php
      $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
      $prev_username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
      if ($error != '') {
          echo "<div class='error-message'>$error</div>";
      }
    ?>

    <form action="process_login.php" method="POST">
      <div class="input-box">
        <i class="fas fa-user icon-left"></i>
        <input type="text" name="username" placeholder="Username" required value="<?php echo $prev_username; ?>">
      </div>
      <div class="input-box">
        <i class="fas fa-lock icon-left"></i>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
      </div>
      <button type="submit" class="login-btn">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
      <a href="forgot_password.php" class="register-link">Forgot your password?</a>
      <a href="register.php" class="register-link">First time user? Register here</a>
    </form>
  </div>

  <!-- FOOTER WITH SOCIAL LINKS -->
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

<script>
  const togglePassword = document.getElementById('togglePassword');
  const password = document.getElementById('password');

  togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);

    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
  });

  // Auto-hide error message after 5 seconds
  window.addEventListener('DOMContentLoaded', () => {
    const errorBox = document.querySelector('.error-message');
    if (errorBox) {
      setTimeout(() => {
        errorBox.style.transition = "opacity 0.5s";
        errorBox.style.opacity = '0';
        setTimeout(() => errorBox.remove(), 500);
      }, 5000); // 5000ms = 5 seconds
    }
  });
</script>

</body>
</html>
