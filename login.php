<?php
session_start();
require 'config.php'; // Use PDO connection

$loginSuccess = false;
$error = "";

if (isset($_POST['login'])) {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
        $error = "✗ Username, email, and password are required!";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "✗ Invalid email format!";
        } else {
            try {
               $sql = "SELECT * FROM users WHERE username = :username OR email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username, 'email' => $email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];          // ✅ Added this
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header("Location: index.php");
    exit;

} else {
    $error = "Username or email not found or password incorrect!";
}

            } catch (PDOException $e) {
                $error = "❌ Login error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
        .form-group i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .form-group .bi-person-fill,
        .form-group .bi-lock-fill,
        .form-group .bi-envelope-fill {
            left: 12px;
        }
        .form-group .show-password {
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
            min-height: 60px;
        }
        #message {
            transition: opacity 0.5s ease;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Login</h2>

    <div class="notification-box" id="messageBox">
        <?php if ($loginSuccess): ?>
            <div id="message" class="alert alert-success fw-bold">
                ✅ Logged in successfully!
            </div>
        <?php elseif (!empty($error)): ?>
            <div id="message" class="alert alert-danger fw-bold">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$loginSuccess): ?>
    <form method="POST">
        <div class="form-group mb-3">
            <i class="bi bi-person-fill"></i>
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
        </div>
        <div class="form-group mb-3">
            <i class="bi bi-envelope-fill"></i>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group mb-3">
            <i class="bi bi-lock-fill"></i>
            <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
            <i class="bi bi-eye-fill show-password" onclick="togglePassword()"></i>
        </div>
        <button type="submit" name="login" class="btn btn-primary">🔐 Login</button>
    </form>
    <?php else: ?>
    <div class="action-buttons">
        <a href="index.php" class="btn btn-success">🏠 Go to Dashboard</a>
        <a href="logout.php" class="btn btn-danger">🔓 Logout</a>
    </div>
    <?php endif; ?>

    <p class="text-muted">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<script>
function togglePassword() {
    const pwd = document.getElementById("password");
    const icon = document.querySelector(".show-password");
    if (pwd.type === "password") {
        pwd.type = "text";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
    } else {
        pwd.type = "password";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
    }
}

setTimeout(function() {
    const msg = document.getElementById("message");
    if (msg) {
        msg.style.opacity = "0";
        setTimeout(() => msg.remove(), 500);
    }
}, 2500);
</script>
</body>
</html>
