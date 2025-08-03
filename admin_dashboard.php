<?php
include 'config.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


// Total posts
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM posts");
$postCount = $stmt->fetch()['total'];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM users");
$userCount = $stmt->fetch()['total'];

// Admins
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM users WHERE role='admin'");
$adminCount = $stmt->fetch()['total'];

// Editors
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM users WHERE role='editor'");
$editorCount = $stmt->fetch()['total'];

// Latest post date
$stmt = $pdo->query("SELECT MAX(created_at) AS latest FROM posts");
$latestPost = $stmt->fetch()['latest'];
?>

<div class="container mt-5">
  <h2 class="mb-4">Admin Dashboard Summary</h2>
  <div class="row">
    <div class="col-md-3">
<div class="card mb-3" style="background-color: #cfe2ff;">
        <div class="card-body">
          <h5 class="card-title">Total Posts</h5>
          <p class="card-text"><?= $postCount ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
<div class="card mb-3" style="background-color: #d1e7dd;">
        <div class="card-body">
          <h5 class="card-title">Total Users</h5>
          <p class="card-text"><?= $userCount ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
<div class="card mb-3" style="background-color: #fff3cd;">
        <div class="card-body">
          <h5 class="card-title">Admins</h5>
          <p class="card-text"><?= $adminCount ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
<div class="card mb-3" style="background-color: #e2e3f3;">
        <div class="card-body">
          <h5 class="card-title">Editors</h5>
          <p class="card-text"><?= $editorCount ?></p>
        </div>
      </div>
    </div>
  </div>
<div class="alert mt-4" style="background-color: #dee2e6;">
    <strong>Latest Post:</strong> <?= date("d M Y", strtotime($latestPost)) ?>
  </div>
</div>
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
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .navbar { border-radius: 8px; }
        .notification-box { min-height: 60px; }
        .card { border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card-footer .btn { border-radius: 6px; }
        .pagination .page-link { border-radius: 6px; }
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
    </style>
</head>
<body>
