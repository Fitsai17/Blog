<?php
global $pdo;
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $user_id]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати пост</title>
    <link rel="stylesheet" href="style.css"> <!-- Підключення стилів -->
</head>
<body>
<div class="container">
    <h1>Додати новий пост</h1>

    <form method="POST" class="post-form">
        <label>Заголовок:</label>
        <input type="text" name="title" placeholder="Заголовок" required><br>

        <label>Вміст поста:</label>
        <textarea name="content" placeholder="Вміст поста" required></textarea><br>

        <button type="submit" class="button button-primary">Додати пост</button>
    </form>

    <a href="index.php">Повернутися на головну</a>
</div>
</body>
</html>
