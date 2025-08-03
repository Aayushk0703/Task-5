<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

// Dashboard summary queries using PDO
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM posts");
$postCount = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM users");
$userCount = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM users WHERE role='admin'");
$adminCount = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM users WHERE role='editor'");
$editorCount = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT MAX(created_at) AS latest FROM posts");
$latestPost = $stmt->fetch()['latest'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #ffecd2, #fcb69f);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
    }
    .container {
      max-width: 1000px;
      margin-top: 40px;
      margin-bottom: 60px;
      padding: 10px;
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .role-badge {
      background-color: #e0d4fd;
      color: #4b0082;
      font-weight: 500;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.9rem;
    }
    .card-header {
      background-color: #343a40;
      color: white;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }
    .btn-icon {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .summary-card {
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .admin-panel-box, .dashboard-summary-box {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      padding: 30px;
      margin-bottom: 30px;
      border-left: 6px solid #6a65c5ff;
    }
    .dashboard-summary-box {
      border-left-color: #00b894;
    }

    /* NEW SECTION HEADER STYLE */
    .section-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 20px;
      border-radius: 10px;
      color: white;
      font-size: 1.5rem;
      font-weight: 600;
      background: linear-gradient(to right, #667eea, #764ba2);
      margin-bottom: 25px;
    }
    .section-header i {
      font-size: 1.8rem;
    }
  </style>
</head>
<body>

<div class="container">

  <!-- Admin Panel Box -->
  <div class="admin-panel-box">
    <div class="section-header">
      🛡️ Admin Panel
    </div>
    <h5 class="mb-2">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</h5>
    <span class="role-badge mb-3 d-inline-block">Role: <?= $_SESSION['role'] ?></span>
    <p class="mb-4">You have full access to manage users and posts.</p>
    <div class="d-flex gap-3 flex-wrap">
      <a href="manage_user.php" class="btn btn-primary btn-icon">👥 Manage Users</a>
      <a href="manage_post.php" class="btn btn-secondary btn-icon">📝 Manage Posts</a>
      <a href="index.php" class="btn btn-outline-dark btn-icon">🔙 Back to Dashboard</a>
    </div>
  </div>

  <!-- Dashboard Summary Box -->
  <div class="dashboard-summary-box">
    <div class="section-header">
      📊 Dashboard Summary
    </div>
    <div class="row">
      <div class="col-md-3">
        <div class="card summary-card mb-3" style="background-color: #CFE2FF;">
          <div class="card-body">
            <h5 class="card-title">Total Posts</h5>
            <p class="card-text"><?= $postCount ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card summary-card mb-3" style="background-color: #D1E7DD;">
          <div class="card-body">
            <h5 class="card-title">Total Users</h5>
            <p class="card-text"><?= $userCount ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card summary-card mb-3" style="background-color: #FFF3CD;">
          <div class="card-body">
            <h5 class="card-title">Admins</h5>
            <p class="card-text"><?= $adminCount ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card summary-card mb-3" style="background-color: #EAD1DC;">
          <div class="card-body">
            <h5 class="card-title">Editors</h5>
            <p class="card-text"><?= $editorCount ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="alert mt-3" style="background-color: #DEE2E6;">
      <strong>Latest Post:</strong> <?= date("d M Y", strtotime($latestPost)) ?>
    </div>
  </div>

</div>

</body>
</html>
