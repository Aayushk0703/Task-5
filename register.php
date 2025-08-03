<?php
session_start();
require 'config.php'; // Use PDO connection

$success = false;
$error = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // ✅ Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "✗ Invalid email format!";
    } elseif (empty($username) || empty($password)) {
        $error = "✗ Username and password are required!";
    } elseif (strlen($password) < 6) {
        $error = "✗ Password must be at least 6 characters!";
    } else {
        try {
            // ✅ Check if username already exists
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['username' => $username]);
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                $error = "❌ Username already exists!";
            } else {
                // ✅ Insert new user with email and role
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'username' => $username,
                    'password' => $hashedPassword,
                    'email' => $email,
                    'role' => 'editor' // default role
                ]);
                $success = true;
            }
        } catch (PDOException $e) {
            $error = "❌ Registration error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-container h2 { 
            margin-bottom: 5px;
            text-align: center;
            color: #333;
        }
        .form-group {
            position: relative;
        }
        .form-group i.bi {  
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 2;
        }
        .form-group .bi-person-fill,
        .form-group .bi-lock-fill,
        .form-group .bi-envelope-fill {
            left: 12px;
        }
        .form-group .toggle-password {
            right: 12px;
            cursor: pointer;
        }
        .form-control {
            border-radius: 8px;
            padding-left: 40px;
            padding-right: 40px;
        }
        .btn {
            width: 100%;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .text-muted {
            text-align: center;
            margin-top: 15px;
        }
        .notification-box {  
            position: relative;
            height: 55px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        #message { 
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        #message.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .login-now-btn { 
            margin-bottom: 10px;
            text-align: center;
        }
        .login-now-btn a { 
            display: inline-block;
            padding: 8px 16px;
            background-color: #198754;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            width:100%;
            text-align: center;
        }
        .login-now-btn a:hover {
            background-color: #146c43;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Register</h2>

    <div class="notification-box" id="messageBox">
        <div id="message" class="alert fw-bold <?= $success ? 'alert-success' : (!empty($error) ? 'alert-danger' : 'hidden') ?>">
            <?= $success ? '✅ Registration successful!' : htmlspecialchars($error) ?>
        </div>
    </div>

    <form method="POST" id="registerForm">
        <div class="form-group mb-3">
            <i class="bi bi-person-fill"></i>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <div class="form-group mb-3">
            <i class="bi bi-envelope-fill"></i>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="form-group mb-3">
            <i class="bi bi-lock-fill"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required minlength="6">
            <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
        </div>

        <?php if ($success): ?>
        <div class="login-now-btn">
            <a href="login.php">🔐 Login Now</a>
        </div>
        <?php endif; ?>

        <button type="submit" name="register" class="btn btn-primary">📝 Register</button>
    </form>

    <p class="text-muted">Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
  const passwordInput = document.getElementById("password");
  const togglePasswordIcon = document.getElementById("togglePassword");
  const registerForm = document.getElementById("registerForm");
  const emailInput = document.getElementById("email");

  togglePasswordIcon.addEventListener("click", function () {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");
  });

  // Client-side email validation before submission
  registerForm.addEventListener("submit", function(e) {
      const emailValue = emailInput.value.trim();
      const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/i;
      if (!emailPattern.test(emailValue)) {
          e.preventDefault();
          alert("✗ Please enter a valid email address.");
      }
  });

  setTimeout(function() {
    const msg = document.getElementById("message");
    if (msg && !msg.classList.contains("hidden")) {
        msg.classList.add("hidden");
    }
  }, 2500);
</script>
</body>
</html>
