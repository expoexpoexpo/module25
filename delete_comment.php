<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = $_POST['comment_id'] ?? null;

    if ($comment_id) {
        $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :id");
        $stmt->execute([':id' => $comment_id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($comment && $comment['user_id'] == $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
            $stmt->execute([':id' => $comment_id]);
        }
    }
}

header('Location: index.php');
exit;
