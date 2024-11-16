<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Головне меню</title>
</head>
<body>
<h1>Вітаємо, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
<a href="logout.php">Вийти</a>
</body>
</html>
