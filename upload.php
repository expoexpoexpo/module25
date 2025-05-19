<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        $targetPath = $targetDir . uniqid() . "_" . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array(mime_content_type($fileTmp), $allowedTypes)) {
            if (move_uploaded_file($fileTmp, $targetPath)) {
             
                $stmt = $pdo->prepare("INSERT INTO images (filename, user_id) VALUES (?, ?)");
                $stmt->execute([$targetPath, $_SESSION['user_id']]);

                $success = true;
            } else {
                $errors[] = "Ошибка при перемещении файла.";
            }
        } else {
            $errors[] = "Можно загружать только JPG, PNG или GIF изображения.";
        }
    } else {
        $errors[] = "Файл не выбран или произошла ошибка при загрузке.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка изображения</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Загрузка изображения</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Изображение успешно загружено!</div>
    <?php endif ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Выберите изображение</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Загрузить</button>
        <a href="index.php" class="btn btn-secondary">На главную</a>
    </form>
</div>

</body>
</html>
