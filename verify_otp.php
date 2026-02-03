<?php $email = $_GET['email'] ?? ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Verify OTP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    /* Same styling as forgot_password.php */
    body {
      margin: 0; padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-box {
      background: rgba(0, 0, 0, 0.92);
      padding: 30px;
      border-radius: 15px;
      width: 95%;
      max-width: 380px;
      box-shadow: 0 0 25px rgba(0, 255, 255, 0.25);
      animation: fadeIn 0.6s ease;
      text-align: center;
    }
    .input-box {
      margin-bottom: 18px;
    }
    .input-box input {
      width: 100%;
      padding: 12px 15px;
      border: none;
      border-radius: 8px;
      background: #e6f5ff;
      color: black;
      font-size: 15px;
      box-sizing: border-box;
    }
    .login-btn {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: none;
      border-radius: 8px;
      background-color: #00d9ff;
      color: black;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
    }
    .login-btn:hover {
      background-color: #00a8cc;
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
  <div class="login-box">
    <h2>Verify OTP</h2>
    <form method="POST" action="check_otp.php">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>" />
      <div class="input-box">
        <input type="text" name="otp" placeholder="Enter OTP" required />
      </div>
      <button type="submit" class="login-btn">Verify</button>
    </form>
  </div>
</body>
</html>
