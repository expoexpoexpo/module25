<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = $_POST['image_id'] ?? null;

    if ($image_id) {
        $stmt = $pdo->prepare("SELECT user_id, filename FROM images WHERE id = :id");
        $stmt->execute([':id' => $image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image && $image['user_id'] == $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE image_id = :image_id");
            $stmt->execute([':image_id' => $image_id]);

            $filePath = $image['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $stmt = $pdo->prepare("DELETE FROM images WHERE id = :id");
            $stmt->execute([':id' => $image_id]);
        }
    }
}

header('Location: index.php');
exit;
