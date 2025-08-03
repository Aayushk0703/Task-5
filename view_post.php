<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    die("❌ Invalid post ID.");
}

$sql = "SELECT posts.*, users.username FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("❌ Post not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin-top: 50px;
            margin-bottom: 60px;
            background-color: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            animation: fadeIn 0.6s ease-in-out;
            border-left: 8px solid #6c63ff; /* Left edge accent */
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Section header with left-side rounded shape */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            color: white;
            font-size: 1.4rem;
            font-weight: 600;
            background: linear-gradient(to right, #6a89cc, #8e44ad);
            margin-bottom: 30px;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            box-shadow: 2px 4px 10px rgba(0,0,0,0.1);
            width: fit-content;
            padding-right: 40px;
        }

        .meta-section {
            background-color: #f5f6fa;
            padding: 20px;
            border-radius: 12px;
            height: 100%;
            border-left: 4px solid #6c63ff;
        }

        .content-section h4 {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .content-section p {
            white-space: pre-wrap;
        }

        .back-btn {
            margin-top: 30px;
        }

        .btn-outline-dark {
            border-radius: 8px;
            padding: 8px 16px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Stylish Section Header -->
    <div class="section-header">
        📄 View Post
    </div>

    <div class="row g-4">
        <!-- Left Column: Meta Info -->
        <div class="col-md-4 meta-section">
            <h5>📌 Post Info</h5>
            <p><strong>Author:</strong> <?= htmlspecialchars($post['username']) ?></p>
            <p><strong>Created At:</strong> <?= date('d M Y, h:i A', strtotime($post['created_at'])) ?></p>
        </div>

        <!-- Right Column: Title & Content -->
        <div class="col-md-8 content-section">
            <h4><?= htmlspecialchars($post['title']) ?></h4>
            <hr>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>
    </div>

    <div class="text-end back-btn">
        <a href="manage_post.php" class="btn btn-outline-dark">🔙 Back to Manage Post</a>
    </div>
</div>

</body>
</html>
