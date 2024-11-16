<?php
global $pdo;
session_start();
require_once 'config.php'; // Підключення до бази даних

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Будь ласка, заповніть всі поля!';
        header('Location: register.php');
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
        ]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;

        // Перехід на головну сторінку
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Помилка реєстрації: ' . $e->getMessage();
        header('Location: register.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація</title>
    <link rel="stylesheet" href="style.css"> <!-- Підключення стилів -->
</head>
<body>
<div class="container">
    <h1>Реєстрація</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="register.php" method="POST" class="register-form">
        <label>Ім'я користувача:</label>
        <input type="text" name="username" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Пароль:</label>
        <input type="password" name="password" required><br>

        <button type="submit" class="button button-primary">Зареєструватися</button>
    </form>

    <p>Вже маєте обліковий запис? <a href="login.php">Увійти</a></p>
</div>
</body>
</html>
