<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$post_id = $_GET['id'] ?? null;
$deleted = 0;

if ($post_id) {
    try {
        if ($role === 'admin') {
            // Admin can delete any post
            $sql = "DELETE FROM posts WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $post_id]);
        } else {
            // Editor can delete only their own post
            $sql = "DELETE FROM posts WHERE id = :id AND user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $post_id, 'user_id' => $user_id]);
        }

        if ($stmt->rowCount() > 0) {
            $deleted = 1;
        }
    } catch (PDOException $e) {
        // Optional: log error or show message
    }
}

// Redirect based on role
$redirect_to = $_GET['redirect_to'] ?? 'index.php';
header("Location: {$redirect_to}?deleted=1");
exit;

?>

