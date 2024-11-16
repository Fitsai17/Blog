<?php
global $pdo;
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Будь ласка, заповніть всі поля!';
        header('Location: login.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Перехід на головну сторінку
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['error'] = 'Неправильний email або пароль!';
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Помилка авторизації: ' . $e->getMessage();
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід</title>
    <link rel="stylesheet" href="style.css"> <!-- Підключаємо CSS -->
</head>
<body>
<div class="container">
    <h1>Вхід</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="login.php" method="POST" class="login-form">
        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Пароль:</label>
        <input type="password" name="password" required><br>

        <button type="submit" class="button button-primary">Увійти</button>
    </form>

    <p>Ще не маєте облікового запису? <a href="register.php">Реєстрація</a></p>
</div>
</body>
</html>
