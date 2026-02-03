<?php
session_start();
include 'db_connect.php'; // ensure this file sets $conn (mysqli)

$name = $email = $message = "";
$error = "";
$success = "";
$rating = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sanitize inputs
    $name = trim($_POST['name'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $message = trim($_POST['message'] ?? "");
    $rating = intval($_POST['rating'] ?? 0);

    // basic validation
    if ($name === "" || $email === "") {
        $error = "Please enter your name and email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($rating < 1 || $rating > 5) {
        $error = "Please provide a rating between 1 and 5 stars.";
    } else {
        // prepare insert
        $stmt = $conn->prepare("INSERT INTO feedbacks (name, email, rating, message) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ssis", $name, $email, $rating, $message);
            if ($stmt->execute()) {
                $success = "Thank you! Your rating has been submitted.";
                // clear input values
                $name = $email = $message = "";
                $rating = 0;
            } else {
                $error = "Failed to submit feedback: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Feedback - Star Rating</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    /* Same background and styling as your other pages */
    body {
      margin: 0; padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
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
    .header img { height: 40px; margin-right: 10px; }
    .header h1 { font-size: 20px; display: flex; align-items: center; margin: 0; }

    .card {
      background: rgba(0, 0, 0, 0.92);
      padding: 30px;
      border-radius: 15px;
      width: 95%;
      max-width: 600px;
      box-shadow: 0 0 25px rgba(0, 255, 255, 0.12);
      animation: fadeIn 0.6s ease;
      text-align: left;
      margin-top: 70px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .card h2 { color: #00d9ff; margin-top: 0; }

    .input-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .input-box {
      position: relative;
      margin-bottom: 12px;
      width: 100%;
    }
    .input-box input, .input-box textarea {
      width: 100%;
      padding: 12px 15px;
      border: none;
      border-radius: 8px;
      background: #e6f5ff;
      color: black;
      font-size: 15px;
      box-sizing: border-box;
      resize: vertical;
    }
    textarea { min-height: 110px; }

    .submit-btn {
      padding: 12px 18px;
      margin-top: 10px;
      border: none;
      border-radius: 8px;
      background-color: #00d9ff;
      color: black;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
    }
    .submit-btn:hover { background-color: #00a8cc; }

    .meta {
      margin-top: 12px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
    }
    .link-small { color:#ccc; text-decoration:none; font-size:14px; }
    .link-small:hover { color:#00d9ff; }

    .error { color: #ff8b8b; margin-bottom: 10px; }
    .success { color: #8bffcc; margin-bottom: 10px; }

    @media (min-width:700px) {
      .half { width: 48%; }
    }

    /* STAR RATING */
    .star-rating {
      direction: rtl; /* for easier hover/focus handling */
      font-size: 28px;
      display: inline-flex;
      gap: 6px;
      user-select: none;
    }
    .star {
      cursor: pointer;
      transition: transform .12s ease;
      color: #444; /* default grey */
    }
    .star.filled {
      color: #ffd166; /* gold-ish */
    }
    .star:hover { transform: scale(1.12); }

    .rating-hint {
      margin-left: 8px;
      font-size: 14px;
      color: #ccc;
      vertical-align: middle;
    }

    /* Hidden radio inputs (if using inputs) */
    input.rating-radio { display: none; }
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
    <h1><img src="assets/logo.png" alt="App Logo"> Student Attendance</h1>
    <span><i class="fas fa-user-shield"></i>Student</span>
  </div>

  <div class="card">
    <h2>Feedback</h2>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="input-row">
        <div class="input-box half">
          <input type="text" name="name" placeholder="Your name" required value="<?php echo htmlspecialchars($name); ?>">
        </div>
        <div class="input-box half">
          <input type="email" name="email" placeholder="Your email" required value="<?php echo htmlspecialchars($email); ?>">
        </div>
      </div>

      <label style="display:block; margin:10px 0 6px; color:#ddd;">Rate us</label>
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
        <div class="star-rating" id="starRating" aria-label="Star rating" role="radiogroup">
          <!-- stars are added via JS; we also include a hidden input to submit numeric value -->
        </div>
        <input type="hidden" id="ratingInput" name="rating" value="<?php echo htmlspecialchars($rating); ?>">
        <span class="rating-hint" id="ratingHint">Select stars</span>
      </div>

      <div class="input-box">
        <textarea name="message" placeholder="Write your feedback here (optional)"><?php echo htmlspecialchars($message); ?></textarea>
      </div>

      <button type="submit" class="submit-btn">Send Feedback</button>

      <div class="meta">
        <a class="link-small" href="student_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <small style="color:#999">We appreciate your suggestions.</small>
      </div>
    </form>
  </div>

<script>
  // Star rating UI (1..5)
  (function(){
    const container = document.getElementById('starRating');
    const hiddenInput = document.getElementById('ratingInput');
    const hint = document.getElementById('ratingHint');
    let current = parseInt(hiddenInput.value) || 0;

    // create 5 star elements (display left-to-right as 1..5)
    for (let i = 1; i <= 5; i++) {
      const span = document.createElement('span');
      span.className = 'star';
      span.dataset.value = i;
      span.innerHTML = '<i class="fa-solid fa-star"></i>';
      span.tabIndex = 0;
      span.setAttribute('role','radio');
      span.setAttribute('aria-checked','false');
      span.setAttribute('aria-label', i + ' star');
      container.appendChild(span);
    }

    const stars = Array.from(container.querySelectorAll('.star'));

    function render(value) {
      stars.forEach((s, idx) => {
        const val = idx + 1;
        if (val <= value) s.classList.add('filled'); else s.classList.remove('filled');
        s.setAttribute('aria-checked', val === value ? 'true' : 'false');
      });
      hiddenInput.value = value;
      if (value === 0) hint.textContent = 'Select stars';
      else hint.textContent = value + (value === 1 ? ' star' : ' stars');
    }

    // initial render
    render(current);

    // click & keyboard handling
    stars.forEach((star, idx) => {
      const val = idx + 1;
      star.addEventListener('click', () => {
        current = val;
        render(current);
      });
      star.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          current = val;
          render(current);
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
          e.preventDefault();
          current = Math.max(1, current - 1);
          render(current);
          stars[current-1].focus();
        } else if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
          e.preventDefault();
          current = Math.min(5, current + 1);
          render(current);
          stars[current-1].focus();
        }
      });
      // hover preview
      star.addEventListener('mouseover', () => {
        render(val);
      });
      star.addEventListener('mouseout', () => {
        render(current);
      });
    });

    // accessibility: allow leaving focus to restore current
    container.addEventListener('focusout', () => {
      render(current);
    });

  })();
</script>
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
