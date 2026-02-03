<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex; justify-content: center; align-items: center;
            height: 100vh; color: #fff;
            flex-direction: column;
            padding-top: 60px;  /* header space */
            padding-bottom: 50px; /* footer space */
            box-sizing: border-box;
        }
        .login-box {
            background: #111; padding: 40px; border-radius: 14px; width: 400px;
            text-align: center; box-shadow: 0 0 25px rgba(0,0,0,0.7);
            animation: fadeIn 0.7s ease;
        }
        @keyframes fadeIn { from {opacity:0; transform:translateY(-20px);} to {opacity:1; transform:translateY(0);} }
        .login-box img { width: 70px; margin-bottom: 15px; }
        h2 { color: #00d9ff; margin-bottom: 25px; font-size: 22px; }
        .input-group { position: relative; margin-bottom: 18px; }
        .input-group input {
            width: 100%; padding: 14px 15px; border-radius: 10px; border: none;
            background: #fff; color: #000; font-size: 15px; box-sizing: border-box;
        }
        .input-group input:focus { outline: none; box-shadow: 0 0 8px #00d9ff; }
        .input-group i { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #555; font-size: 16px; }
        button {
            background: #00d9ff; border: none; padding: 14px; border-radius: 10px;
            width: 100%; cursor: pointer; color: #000; font-weight: bold; font-size: 16px; transition: 0.3s;
        }
        button:hover { background: #00a8cc; transform: scale(1.03); }
        .extra-links { margin-top: 15px; font-size: 14px; }
        .extra-links a { color: #00d9ff; text-decoration: none; font-weight: bold; }
        .extra-links a:hover { text-decoration: underline; }

        /* Error message styles + fade-out support */
        .error-msg {
            background: #ff4d4f; color: #fff; padding: 10px 12px; border-radius: 8px;
            margin-bottom: 15px; font-weight: 600;
            box-shadow: 0 6px 14px rgba(255,77,79,0.3);
            opacity: 1; transform: translateY(0);
            transition: opacity .6s ease, transform .6s ease;
        }
        .error-msg.hide {
            opacity: 0; transform: translateY(-10px); pointer-events: none;
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
            height: 40px;
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
            padding: 8px 0;
            font-size: 14px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
     <div class="header">
        <div class="header-left">
            <img src="assets/logo.png" alt="App Logo">
            <h1>Student Attendance</h1>
        </div>
        <div class="header-right">
            <i class="fas fa-user-graduate"></i> Student
        </div>
    </div>

    <div class="login-box">
        <img src="assets/logo.png" alt="App Logo">
        <h2><i class="fas fa-user-graduate"></i> Student Login</h2>

        <!-- Error Msg (auto-hide) -->
        <?php if(isset($_SESSION['error_msg'])): ?>
            <div id="errorMsg" class="error-msg" role="alert" aria-live="polite">
                <?php echo $_SESSION['error_msg']; ?>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <form method="POST" action="stud_process_login.php" novalidate>
            <div class="input-group">
                <input type="text" name="student_name" placeholder="Enter Student Name" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <input type="text" name="roll_no" placeholder="Enter Roll Number" required>
                <i class="fas fa-id-card"></i>
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
    </div>

    <div class="footer">
        © 2025 Student Attendance App. All Rights Reserved.
    </div>

<script>
  // Auto-hide error after 2.5s
  const err = document.getElementById('errorMsg');
  if (err) {
    setTimeout(() => {
      err.classList.add('hide');
      // remove from DOM after transition
      setTimeout(() => { if (err && err.parentNode) err.parentNode.removeChild(err); }, 700);
    }, 2500);
  }
</script>
</body>
</html>
