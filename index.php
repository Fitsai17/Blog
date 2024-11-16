<?php
global $pdo;
session_start();
require_once 'config.php';

// Перевірка сесії
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Отримання постів
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
$posts = $stmt->fetchAll();

// Обробка додавання коментаря
if (isset($_POST['comment_content']) && isset($_POST['post_id'])) {
    $comment_content = trim($_POST['comment_content']);
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    if (!empty($comment_content)) {
        // Вставляємо коментар в базу даних
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)");
        $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id,
            ':content' => $comment_content
        ]);
    }
    // Перенаправляємо назад на головну
    header('Location: index.php');
    exit;
}

// Отримуємо коментарі для кожного поста
$comments = [];
$stmt = $pdo->query("SELECT * FROM comments JOIN users ON comments.user_id = users.id ORDER BY comments.created_at ASC");
while ($row = $stmt->fetch()) {
    $comments[$row['post_id']][] = $row;
}

// Обробка видалення поста
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Перевірка, чи пост належить користувачу
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $delete_id, ':user_id' => $_SESSION['user_id']]);
    $post = $stmt->fetch();

    if ($post) {
        // Видаляємо пост з бази даних
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);

        // Перенаправляємо на головну сторінку
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Цей пост не можна видалити!';
        header('Location: index.php');
        exit;
    }
}

// Обробка видалення коментаря
if (isset($_GET['delete_comment_id'])) {
    $delete_comment_id = $_GET['delete_comment_id'];

    // Перевірка, чи коментар належить користувачу
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $delete_comment_id, ':user_id' => $_SESSION['user_id']]);
    $comment = $stmt->fetch();

    if ($comment) {
        // Видаляємо коментар з бази даних
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
        $stmt->execute([':id' => $delete_comment_id]);

        // Перенаправляємо назад на головну сторінку
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Цей коментар не можна видалити!';
        header('Location: index.php');
        exit;
    }
}
?>

<?php include('header.php'); ?>

<?php if (!empty($_SESSION['error'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php foreach ($posts as $post): ?>
    <div class="post">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p><?= htmlspecialchars($post['content']) ?></p>
        <small>Автор: <?= htmlspecialchars($post['username']) ?> | <?= $post['created_at'] ?></small>

        <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
            <div>
                <a href="edit_post.php?id=<?= $post['id'] ?>">Редагувати</a> |
                <a href="index.php?delete_id=<?= $post['id'] ?>" onclick="return confirm('Видалити цей пост?')">Видалити</a>
            </div>
        <?php endif; ?>

        <!-- Форма для додавання коментаря -->
        <form action="index.php" method="POST">
            <textarea name="comment_content" required placeholder="Додати коментар..."></textarea><br>
            <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
            <button type="submit">Додати коментар</button>
        </form>

        <!-- Виведення коментарів -->
        <h3>Коментарі:</h3>
        <?php if (isset($comments[$post['id']])): ?>
            <?php foreach ($comments[$post['id']] as $comment): ?>
                <div class="comment">
                    <p><strong><?= htmlspecialchars($comment['username']); ?>:</strong> <?= htmlspecialchars($comment['content']); ?></p>
                    <small><?= $comment['created_at']; ?></small>
                    <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                        <br><a href="index.php?delete_comment_id=<?= $comment['id'] ?>" onclick="return confirm('Видалити цей коментар?')">Видалити коментар</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Немає коментарів.</p>
        <?php endif; ?>
    </div>
    <hr>
<?php endforeach; ?>

<?php include('footer.php'); ?>
