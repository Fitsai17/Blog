<?php
global $pdo;
require_once 'config.php';
session_start();

// Перевірка авторизації
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Перевірка методу запиту
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    // Перевірка полів
    if (empty($title) || empty($content)) {
        $_SESSION['error'] = 'Будь ласка, заповніть всі поля!';
        header('Location: dashboard.php');
        exit;
    }

    // Додавання поста
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (:user_id, :title, :content, NOW())");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'content' => $content,
        ]);

        $_SESSION['success'] = 'Пост успішно додано!';
        header('Location: dashboard.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Помилка додавання поста: ' . $e->getMessage();
        header('Location: dashboard.php');
        exit;
    }
} else {
    header('Location: dashboard.php');
    exit;
}
