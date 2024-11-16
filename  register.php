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
    <title>Реєстрація</title>
</head>
<body>
<h1>Реєстрація</h1>
<?php if (!empty($_SESSION['error'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<form action="register.php" method="POST">
    <label>Ім'я користувача: <input type="text" name="username" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Пароль: <input type="password" name="password" required></label><br>
    <button type="submit">Зареєструватися</button>
</form>
<p>Вже маєте обліковий запис? <a href="login.php">Увійти</a></p>
</body>
</html>
