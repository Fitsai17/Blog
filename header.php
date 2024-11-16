<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Головна сторінка</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Вітаємо, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <nav>
            <a href="add_post.php">Додати пост</a> |
            <a href="logout.php">Вийти</a>
        </nav>
    </header>
    <hr>
