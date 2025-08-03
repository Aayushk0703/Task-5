<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$post_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$error = "";
$success = false;

// Fetch post for editing
if ($post_id) {
    try {
        if ($role === 'admin') {
            $sql = "SELECT * FROM posts WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $post_id]);
        } else {
            $sql = "SELECT * FROM posts WHERE id = :id AND user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $post_id, 'user_id' => $user_id]);
        }

        $post = $stmt->fetch();
        if (!$post) {
            die("❌ Post not found or unauthorized access.");
        }
    } catch (PDOException $e) {
        die("❌ Error fetching post: " . $e->getMessage());
    }
} else {
    die("❌ Invalid post ID.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        try {
            $sql = "UPDATE posts SET title = :title, content = :content WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'title' => $title,
                'content' => $content,
                'id' => $post_id
            ]);
            $success = true;
        } catch (PDOException $e) {
            $error = "❌ Error updating post: " . $e->getMessage();
        }
    } else {
        $error = "❌ All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 40px 15px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            max-width: 700px;
            width: 100%;
            background-color: #fff;
            padding: 25px 30px 35px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            border-left: 8px solid #7ab6f9;
            box-sizing: border-box;
            animation: fadeIn 0.6s ease-in-out;
            position: relative;
        }

        .header-bar {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            font-weight: 600;
            font-size: 1.4rem;
            padding: 15px 25px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 12px;
            user-select: none;
             margin: 5px -10px 5px -10px;
            box-shadow: 0 4px 8px rgba(106,17,203,0.4);
        }

        .header-bar span {
            font-size: 1.8rem;
            line-height: 1;
        }

        .notification-box {
            min-height: 60px;
            margin-bottom: 20px;
        }

        .alert {
  margin: 10px -5px 25px -5px; /* same left/right as header-bar */
  padding: 15px 25px;
  font-size: 1rem;
  font-weight: 600;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
        }

        label.form-label {
            font-weight: 600;
        }

        input.form-control,
        textarea.form-control {
            border-radius: 8px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input.form-control:focus,
        textarea.form-control:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.4);
            outline: none;
        }

        .d-flex.justify-content-between {
            margin-top: 1.5rem;
        }

        .btn-primary {
            font-weight: 600;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <span>✏️</span> Edit Post
    </div>

    <div class="notification-box" id="messageBox">
        <?php if ($success): ?>
            <div class="alert alert-success" id="message">✅ Post updated successfully!</div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger" id="message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
       <?php
$allowed_redirects = ['index.php', 'manage_post.php'];
$redirect_to = in_array($_GET['redirect_to'] ?? '', $allowed_redirects) ? $_GET['redirect_to'] : 'index.php';
?>
...
<div class="d-flex justify-content-between">
    <button type="submit" class="btn btn-primary">💾 Update Post</button>
    <a href="<?= htmlspecialchars($redirect_to) ?>" class="btn btn-outline-dark">🔙 Go Back</a>
</div>

    </form>
</div>

<script>
    setTimeout(function () {
        const msg = document.getElementById("message");
        if (msg) {
            msg.style.transition = "opacity 0.5s ease";
            msg.style.opacity = "0";
            setTimeout(() => msg.remove(), 500);
        }
    }, 3000);
</script>
</body>
</html>
