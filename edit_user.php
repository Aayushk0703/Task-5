<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch user data
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: manage_user.php?error=notfound");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    $updateSql = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        'username' => $username,
        'email' => $email,
        'role' => $role,
        'id' => $id
    ]);

    header("Location: manage_user.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
            margin-bottom: 60px;
            background-color: #fff;
            padding: 35px;
            border-left: 8px solid #8ec5fc;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 1rem 1.5rem;
            font-size: 1.4rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0;
        }

        .form-label {
            font-weight: 500;
        }
    .btn-primary {
        background: linear-gradient(to right, #667eea, #764ba2);
        border: none;
        transition: background 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(to right, #764ba2, #667eea);
    }



        .btn-secondary {
            background: #ccc;
            border: none;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .form-select {
            border-radius: 6px;
        }

        input.form-control {
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header">
                ✏️ Edit User
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Update</button>
                    <a href="manage_user.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
