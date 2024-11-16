<?php
global $pdo;
session_start();
require_once 'config.php';

// Перевірка, чи користувач увійшов
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Перевірка, чи є ID поста в URL
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$post_id = $_GET['id'];

// Отримуємо пост з бази даних
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $post_id, ':user_id' => $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $_SESSION['error'] = 'Будь ласка, заповніть всі поля!';
    } else {
        // Оновлюємо пост в базі даних
        $stmt = $pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
        $stmt->execute([':title' => $title, ':content' => $content, ':id' => $post_id]);

        // Перенаправлення на головну сторінку після збереження
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагувати пост</title>
    <link rel="stylesheet" href="style.css"> <!-- Підключаємо CSS -->
</head>
<body>
<div class="container">
    <h1>Редагувати пост</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="edit_post.php?id=<?= $post['id']; ?>" method="POST" class="edit-post-form">
        <label>Заголовок:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']); ?>" required><br>

        <label>Контент:</label>
        <textarea name="content" required><?= htmlspecialchars($post['content']); ?></textarea><br>

        <button type="submit" class="button button-primary">Оновити пост</button>
    </form>

    <a href="index.php" class="button button-secondary">Повернутися на головну</a>
</div>
</body>
</html>
