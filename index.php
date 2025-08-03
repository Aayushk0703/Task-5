<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$user_id = $_SESSION['user_id'];
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Count total posts for pagination
if (!empty($search)) {
    $countSql = "SELECT COUNT(*) FROM posts WHERE user_id = :user_id AND (title LIKE :search OR content LIKE :search)";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
} else {
    $countSql = "SELECT COUNT(*) FROM posts WHERE user_id = :user_id";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
}
$countStmt->execute();
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);

// Fetch posts securely
if (!empty($search)) {
    $sql = "SELECT * FROM posts WHERE user_id = :user_id AND (title LIKE :search OR content LIKE :search) ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
} else {
    $sql = "SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
}
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin-top: 40px;
            margin-bottom: 60px;
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            background-color: rgba(255, 255, 255, 0.95);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* White box inside container with left colored border and rounded left corners */
        .inner-box {
            background: white;
            padding: 30px;
            border-left: 10px solid #6a11cb;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Colored header on welcome section */
        .welcome-header {
            background-color: #6a11cb;
            color: white;
            padding: 15px 25px;
            font-weight: 700;
            font-size: 1.25rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            margin-bottom: 1rem;
            user-select: none;
        }

        .navbar {
            border-radius: 8px;
        }
        .notification-box { 
            min-height: 60px;
            text-align: center;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-footer .btn {
            border-radius: 6px;
        }
        .pagination .page-link {
            border-radius: 6px;
        }
        .classy-logout {
            background: linear-gradient(to right, rgb(88, 73, 203), rgb(241, 151, 124));
            color: white; border: none; border-radius: 8px;
            padding: 8px 16px; font-weight: 500;
            transition: background 0.3s ease;
        }
        .classy-logout:hover {
            background: linear-gradient(to right, rgb(88, 73, 203), rgb(241, 151, 124));
        }
        .classy-search {
            background: linear-gradient(to right, rgb(196, 137, 185), rgb(241, 151, 124));
            color: white; border: none; border-radius: 8px;
            padding: 8px 16px; font-weight: 500;
            transition: background 0.3s ease;
        }
        .role-badge {
            background-color: #e0d4fd; /* Soft lavender */
            color: #4b0082;            /* Deep indigo */
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 px-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">📝 Blog Dashboard</a>
            <div class="d-flex">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_panel.php" class="btn btn-outline-light me-2">🛠️ Admin Panel</a>
                <?php endif; ?>
                <a href="add_post.php" class="btn btn-outline-light me-2">➕ Add Post</a>
                <a href="logout.php" class="btn classy-logout">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <!-- White box with left border -->
    <div class="inner-box">

        <!-- Welcome Header Bar -->
        <div class="welcome-header">
            Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
        </div>

        <?php if (isset($_SESSION['role'])): ?>
            <span class="role-badge">Role: <?= htmlspecialchars($_SESSION['role']) ?></span>
        <?php endif; ?>

        <!-- Success Message -->
        <div class="notification-box" id="messageBox">
            <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
                <div class="alert alert-success fw-bold" id="message">
                    ✅ Post deleted successfully!
                </div>
            <?php endif; ?>
        </div>

        <p class="text-muted mb-4">Here you can manage your blog posts.</p>

        <!-- Search Form -->
        <form method="GET" action="" class="d-flex mb-4">
            <input type="text" name="search" class="form-control me-2" placeholder="Search posts..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn classy-search">🔍 Search</button>
        </form>

        <!-- Posts List -->
        <?php if ($posts): ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        <small class="text-muted">📅 Posted on <?= $post['created_at'] ?></small>
                    </div>
                    <div class="card-footer text-end">
                        <a href="edit_post.php?id=<?= $post['id'] ?>&redirect_to=index.php" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                        <a href="delete_post.php?id=<?= $post['id'] ?>&redirect_to=index.php" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning fw-bold">
                😢 No posts found
                <?php if (!empty($search)): ?>
                    matching <strong><?= htmlspecialchars($search) ?></strong>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div> <!-- end inner-box -->
</div> <!-- end container -->

<!-- Auto-hide success message -->
<script>
    setTimeout(function () {
        const msg = document.getElementById("message");
        if (msg) {
            msg.style.transition = "opacity 0.5s ease";
            msg.style.opacity = "0";
            setTimeout(() => msg.remove(), 500);
        }
    }, 2000);
</script>
</body>
</html>
