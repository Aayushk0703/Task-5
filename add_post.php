<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require 'config.php'; // Use PDO connection
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (!empty($title) && !empty($content)) {
        try {
            $sql = "INSERT INTO posts (title, content, user_id, created_at) VALUES (:title, :content, :user_id, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'title' => $title,
                'content' => $content,
                'user_id' => $user_id
            ]);
            $success = true;
        } catch (PDOException $e) {
            $error = "❌ Error adding post: " . $e->getMessage();
        }
    } else {
        $error = "❌ All fields are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
            border-left: 8px solid #7ab6f9; /* Light blue left border */
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
            box-sizing: border-box;
            box-shadow: 0 4px 8px rgba(106,17,203,0.4);
        }
        .header-bar span {
            font-size: 1.8rem;
            line-height: 1;
        }
        .notification-box {
  min-height: 70px;  /* reserves space */
  margin: 10px -30px -10px -30px;  /* same horizontal spacing as header */
}
.alert {
  margin: 0 25px 25px 25px; /* same left/right as header-bar */
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
        .btn-success {
            font-weight: 600;
            padding: 0.5rem 1.25rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <span>➕</span> Add New Post
    </div>

    <div class="notification-box" id="messageBox">
        <?php if ($success): ?>
            <div class="alert alert-success fw-bold" id="message">
                ✅ Post added successfully!
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger fw-bold" id="message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input id="title" type="text" name="title" class="form-control" placeholder="Enter post title" required />
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea id="content" name="content" class="form-control" rows="6" placeholder="Write your post here..." required></textarea>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">📝 Submit Post</button>
            <a href="index.php" class="btn btn-outline-dark">🏠 Go to Dashboard</a>
        </div>
    </form>
</div>

<script>
  setTimeout(function() {
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
