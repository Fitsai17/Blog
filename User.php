<?php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Реєстрація користувача
    public function register($username, $email, $password)
    {
        // Перевірка, чи вже існує користувач із цим email
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            return "Email вже зареєстровано!";
        }

        // Хешування пароля
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Вставка нового користувача в базу
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        return "Реєстрація успішна!";
    }

    // Авторизація користувача
    public function login($email, $password)
    {
        // Знайти користувача по email
        $stmt = $this->pdo->prepare("SELECT id, password FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Почати сесію і зберегти ідентифікатор користувача
            session_start();
            $_SESSION['user_id'] = $user['id'];
            return "Авторизація успішна!";
        }

        return "Невірний email або пароль.";
    }

    // Отримання інформації про поточного користувача
    public function getUser($userId)
    {
        $stmt = $this->pdo->prepare("SELECT id, username, email FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch();
    }

    // Вихід із системи
    public function logout()
    {
        session_start();
        session_destroy();
        return "Ви вийшли із системи.";
    }
}

