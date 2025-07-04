<?php
require_once 'includes/auth.php';
require_login();

$db = new SQLite3('myapp.db');
$user_id = $_SESSION['user']['id'];
$post_id = intval($_GET['id'] ?? 0);

// Fetch post
$stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');
$stmt->bindValue(':id', $post_id, SQLITE3_INTEGER);
$post = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
if (!$post) {
    exit("Post not found.");
}

// Handle comment submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if ($content !== '') {
        $stmt = $db->prepare('INSERT INTO comments (post_id, user_id, c>
        $stmt->bindValue(':post', $post_id, SQLITE3_INTEGER);
        $stmt->bindValue(':user', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->execute();
        header("Location: view_post.php?id=" . $post_id);
        exit;
    }
}

// Get comments
$stmt = $db->prepare('SELECT c.*, u.username FROM comments c JOIN users>
$stmt->bindValue(':post', $post_id, SQLITE3_INTEGER);
$comments = $stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="myapp.css">
</head>
<body>
<div class="container">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <img src="uploads/<?= htmlspecialchars($post['image']) ?>" style="w>
    <p>👁️ <?= $post['view_count'] ?> | ❤️ <?= $post['likes'] ?></p>

    <h3>Comments</h3>
    <form method="post">
        <textarea name="content" placeholder="Write a comment..." requi>
        <br>
        <button type="submit">💬 Comment</button>
    </form>

    <div class="comments">
        <?php while ($row = $comments->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="comment-box">
                <strong><?= htmlspecialchars($row['username']) ?></stro>
                <p><?= htmlspecialchars($row['content']) ?></p>
                <small><?= $row['created_at'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <p><a href="home.php">← Back to gallery</a></p>
</div>
</body>
</html>
