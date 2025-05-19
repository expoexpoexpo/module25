<?php
require_once 'db.php';
session_start();

$stmt = $pdo->query("
    SELECT images.id, images.filename, users.username, images.user_id
    FROM images 
    JOIN users ON images.user_id = users.id
    ORDER BY images.id DESC
");
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->query("
    SELECT comments.id, comments.text, comments.created_at, comments.user_id, comments.image_id, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id
    ORDER BY comments.created_at ASC
");
$commentsRaw = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$comments = [];
foreach ($commentsRaw as $comment) {
    $comments[$comment['image_id']][] = $comment;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Галерея изображений с комментариями</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-4">
    <h1 class="mb-4">Галерея изображений</h1>

    <?php if (isset($_SESSION['username'])): ?>
        <p>Привет, <strong><?=htmlspecialchars($_SESSION['username'])?></strong>! <a href="upload.php">Загрузить изображение</a> | <a href="logout.php">Выйти</a></p>
    <?php else: ?>
        <p><a href="login.php">Войти</a> или <a href="register.php">Зарегистрироваться</a> чтобы загружать и комментировать изображения.</p>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($images as $img): ?>
        <div class="col">
            <div class="card h-100 shadow-sm">
                <img src="<?=htmlspecialchars($img['filename'])?>" class="card-img-top" alt="Изображение">
                <div class="card-body">
                    <p class="card-text"><small class="text-muted">Загружено пользователем: <?=htmlspecialchars($img['username'])?></small></p>

                    <h6>Комментарии:</h6>
                    <?php if (!empty($comments[$img['id']])): ?>
                        <ul class="list-unstyled">
                            <?php foreach ($comments[$img['id']] as $c): ?>
                                <li>
                                    <strong><?=htmlspecialchars($c['username'])?></strong> (<?=htmlspecialchars($c['created_at'])?>):<br>
                                    <?=nl2br(htmlspecialchars($c['text']))?>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                                        <form action="delete_comment.php" method="post" style="display:inline;">
                                            <input type="hidden" name="comment_id" value="<?=htmlspecialchars($c['id'])?>">
                                            <button type="submit" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="return confirm('Удалить комментарий?')">Удалить</button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p><em>Комментариев пока нет.</em></p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $img['user_id']): ?>
                        <form action="delete_image.php" method="post" onsubmit="return confirm('Удалить это изображение?');" class="mt-2">
                            <input type="hidden" name="image_id" value="<?=htmlspecialchars($img['id'])?>">
                            <button type="submit" class="btn btn-danger btn-sm">Удалить изображение</button>
                        </form>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="add_comment.php" method="post" class="mt-3">
                            <div class="mb-3">
                                <textarea name="comment_text" class="form-control" rows="2" placeholder="Оставьте комментарий..." required></textarea>
                                <input type="hidden" name="image_id" value="<?=htmlspecialchars($img['id'])?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Отправить</button>
                        </form>
                    <?php else: ?>
                        <p><em>Авторизуйтесь, чтобы оставлять комментарии.</em></p>
                    <?php endif; ?>

                </div> 
            </div>
        </div>
    <?php endforeach; ?>
</div>