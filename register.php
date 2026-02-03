<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register - Student Attendance App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Font Awesome for icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />

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
        overflow: hidden;
      }

      .register-box {
        background: rgba(0, 0, 0, 0.85);
        padding: 35px;
        border-radius: 12px;
        width: 95%;
        max-width: 450px;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        animation: fadeIn 0.9s ease;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: scale(0.9);
        }
        to {
          opacity: 1;
          transform: scale(1);
        }
      }

      h2 {
        margin-bottom: 20px;
        color: #00d9ff;
        font-size: 26px;
      }

      img {
        width: 120px;
        margin-bottom: 15px;
      }

      .input-box {
        position: relative;
        margin-bottom: 18px;
      }

      .input-box input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: none;
        border-radius: 8px;
        background: #222;
        color: white;
        font-size: 15px;
        box-sizing: border-box;
        line-height: 1.3;
      }

      .input-box i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #00d9ff;
        font-size: 18px;
        pointer-events: none;
      }

      .input-box input:focus {
        outline: 2px solid #00d9ff;
        background: #2c2c2c;
      }

      button {
        width: 48%;
        padding: 12px;
        margin-top: 15px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        cursor: pointer;
      }

      .submit-btn {
        background-color: #00d9ff;
        color: #000;
      }

      .submit-btn:hover {
        background-color: #00a8cc;
      }

      .clear-btn {
        background-color: #dc3545;
        color: #fff;
      }

      .clear-btn:hover {
        background-color: #a71d2a;
      }
      .login-link {
            margin-top: 18px;
            display: block;
            font-size: 13px;
            color: #ccc;
            text-align: center;
            text-decoration: none;
        }

        .login-link:hover {
            color: #00d9ff;
        }

      @media (max-width: 500px) {
        .register-box {
          padding: 25px;
        }

        h2 {
          font-size: 22px;
        }

        button {
          width: 100%;
          margin-bottom: 10px;
        }
      }
    </style>

    <script>
      function validateForm() {
        let username = document.forms['regForm']['username'].value;
        let password = document.forms['regForm']['password'].value;
        let cpassword = document.forms['regForm']['confirm_password'].value;
        let contact = document.forms['regForm']['contact'].value;
        let email = document.forms['regForm']['email'].value;

        const usernameRegex = /^[A-Za-z]+$/;
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=]).{6,}$/;
        const contactRegex = /^\d{10}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!usernameRegex.test(username)) {
          alert('Username must contain letters only.');
          return false;
        }

        if (!passwordRegex.test(password)) {
          alert(
            'Password must contain at least one uppercase letter, one number, and one special character.'
          );
          return false;
        }

        if (password !== cpassword) {
          alert('Passwords do not match.');
          return false;
        }

        if (!contactRegex.test(contact)) {
          alert('Contact number must be exactly 10 digits.');
          return false;
        }

        if (!emailRegex.test(email)) {
          alert('Invalid email format.');
          return false;
        }

        return true;
      }

      function clearForm() {
        document.getElementById('regForm').reset();
      }
    </script>
  </head>
  <body>
    <div class="register-box">
      <!-- Correct relative image path -->
      <img src="assets/logo-removebg-preview.png" alt="Logo" />
      <h2>Register - Student Attendance App</h2>
      <form
        name="regForm"
        id="regForm"
        action="process_register.php"
        method="POST"
        onsubmit="return validateForm();"
      >
        <div class="input-box">
          <input type="text" name="username" placeholder="Username" required />
          <i class="fas fa-user"></i>
        </div>
        <div class="input-box">
          <input
            type="password"
            name="password"
            placeholder="Password"
            required
          />
          <i class="fas fa-lock"></i>
        </div>
        <div class="input-box">
          <input
            type="password"
            name="confirm_password"
            placeholder="Confirm Password"
            required
          />
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="input-box">
          <input
            type="text"
            name="contact"
            placeholder="Contact Number"
            required
          />
          <i class="fas fa-phone"></i>
        </div>
        <div class="input-box">
          <input type="email" name="email" placeholder="Email" required />
          <i class="fas fa-envelope"></i>
        </div>
        <button type="submit" class="submit-btn">
          <i class="fas fa-user-plus"></i> Submit
        </button>
        <button type="button" class="clear-btn" onclick="clearForm()">
          <i class="fas fa-eraser"></i> Clear
        </button>
          <a href="login.php" class="login-link">Already registered? Login here</a>
      </form>
    </div>
  </body>
</html>
