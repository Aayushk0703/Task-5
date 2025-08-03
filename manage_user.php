<?php
session_start();
$deleted = isset($_GET['deleted']) ? (int)$_GET['deleted'] : 0;
$updated = isset($_GET['updated']) ? (int)$_GET['updated'] : 0;

require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

// Fetch all users
$sql = "SELECT id, username, email, role FROM users ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
             background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .container {
            max-width: 950px;
            margin-top: 40px;
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

        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            background: #fdfdfd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 1.2rem 1.5rem;
            font-size: 1.4rem;
            font-weight: 600;
        }
        .badge.bg-primary {
            background-color: #b39ddb !important;
        }
        .badge.bg-secondary {
            background-color: #cfd8dc !important;
            color: #000;
        }
        .badge.bg-dark {
            background-color: #d1c4e9 !important;
            color: #000;
            font-weight: 500;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }

        .table th {
            background-color: #e7ecff;
            color: #333;
            font-weight: 500;
        }

        .table td, .table th {
            vertical-align: middle !important;
        }

        .btn-sm {
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .alert {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            font-weight: 500;
        }
        .notification-box {
    min-height: 60px;
    margin-bottom: 20px;
}


    </style>
</head>
<body>
    
 

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header">
                👥 Manage Users
            </div>
            <div class="card-body">
                <p class="mb-3">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
                <span class="badge bg-dark mb-4">Role: Admin</span>

                <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success fw-bold text-center" id="message">
        ✅ User deleted successfully!
    </div>
<?php endif; ?>

                <?php if ($updated === 1): ?>
                <div class="alert alert-info fw-bold text-center" role="alert" id="updateAlert">
                    ✏️ User updated successfully!
                            </div>
                <?php endif; ?>
<script>
  setTimeout(function () {
    const msg = document.getElementById("message");
    if (msg) {
      msg.style.transition = "opacity 0.5s ease";
      msg.style.opacity = "0";
      setTimeout(() => msg.remove(), 500);
    }

    const updateAlert = document.getElementById("updateAlert");
    if (updateAlert) {
      updateAlert.style.transition = "opacity 0.5s ease";
      updateAlert.style.opacity = "0";
      setTimeout(() => updateAlert.remove(), 500);
    }
  }, 2000);
</script>



                <table class="table table-bordered table-hover mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="badge <?= $user['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-success">Edit</a>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a href="admin_panel.php" class="btn btn-outline-dark mt-3">🔙 Back to Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
