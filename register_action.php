<?php
global $pdo;
require_once 'config.php';
session_start();

// Перевірка, чи надіслані дані
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Перевірка наявності даних
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Будь ласка, заповніть всі поля!';
        header('Location: register.php');
        exit;
    }

    // Хешування пароля
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Додавання користувача до бази даних
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);

        // Запис інформації користувача в сесію
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;

        // Перехід на головне меню
        header('Location: dashboard.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Помилка реєстрації: ' . $e->getMessage();
        header('Location: register.php');
        exit;
    }
} else {
    header('Location: register.php');
    exit;
}
