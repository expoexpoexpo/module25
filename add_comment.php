<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = $_POST['image_id'] ?? null;
    $comment_text = trim($_POST['comment_text'] ?? '');

    if ($image_id && $comment_text !== '') {
        $stmt = $pdo->prepare("INSERT INTO comments (image_id, user_id, text) VALUES (:image_id, :user_id, :text)");
        $stmt->execute([
            ':image_id' => $image_id,
            ':user_id' => $_SESSION['user_id'],
            ':text' => $comment_text
        ]);
    }
}

header('Location: index.php');
exit;
