<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Prevent deleting self
    if ($id === $_SESSION['user_id']) {
        header("Location: manage_user.php?error=self-delete");
        exit;
    }

    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    header("Location: manage_user.php?deleted=1");
    exit;
}
?>
