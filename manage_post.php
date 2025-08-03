<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

$deleted = isset($_GET['deleted']) ? (int)$_GET['deleted'] : 0;
$updated = isset($_GET['updated']) ? (int)$_GET['updated'] : 0;

$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Posts</title>
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

        .badge.bg-dark {
            background-color: #d1c4e9 !important;
            color: #000;
            font-weight: 500;
            margin-bottom: 10px;
            display: inline-block;
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
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header">
                📝 Manage Posts
            </div>
            <div class="card-body">
                <p class="mb-1">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>

                <!-- Role badge -->
                <span class="badge bg-dark">Role: Admin</span>

                <!-- Notifications -->
                <div class="notification-box mt-3">
                    <?php if ($deleted === 1): ?>
                        <div class="alert alert-success fw-bold text-center" id="deleteAlert">
                            ✅ Post deleted successfully!
                        </div>
                    <?php endif; ?>

                    <?php if ($updated === 1): ?>
                        <div class="alert alert-info alert-dismissible fade show text-center" role="alert" id="updateAlert">
                            ✏️ Post updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Alert fade-out -->
                <script>
                    setTimeout(() => {
                        const deleteAlert = document.getElementById("deleteAlert");
                        if (deleteAlert) {
                            deleteAlert.style.transition = "opacity 0.5s ease";
                            deleteAlert.style.opacity = "0";
                            setTimeout(() => deleteAlert.remove(), 500);
                        }

                        const updateAlert = document.getElementById("updateAlert");
                        if (updateAlert) {
                            updateAlert.style.transition = "opacity 0.5s ease";
                            updateAlert.style.opacity = "0";
                            setTimeout(() => updateAlert.remove(), 500);
                        }
                    }, 2000);
                </script>

                <!-- Posts Table -->
                <table class="table table-bordered table-hover mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td><?= htmlspecialchars($post['title']) ?></td>
                            <td><?= htmlspecialchars($post['username']) ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($post['created_at'])) ?></td>
                            <td>
                                <a href="edit_post.php?id=<?= $post['id'] ?>&redirect_to=manage_post.php" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="delete_post.php?id=<?= $post['id'] ?>&redirect_to=manage_post.php" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-success">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a href="admin_panel.php" class="btn btn-outline-dark mt-4">🔙 Back to Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
