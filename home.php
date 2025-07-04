<?php
require_once 'includes/auth.php';
require_login();

$db = new SQLite3('myapp.db');
$user_id = $_SESSION['user']['id'];

$limit = 5;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');
$search_sql = '';
$params = [];

if ($search !== '') {
    $search_sql = "WHERE title LIKE :search";
    $params[':search'] = "%$search%";
}

$count_stmt = $db->prepare("SELECT COUNT(*) as count FROM posts $search_sql");
foreach ($params as $k => $v) {
    $count_stmt->bindValue($k, $v, SQLITE3_TEXT);
}
$total_count = $count_stmt->execute()->fetchArray(SQLITE3_ASSOC)['count'];
$total_pages = ceil($total_count / $limit);

$query = "SELECT posts.*, users.username, users.id AS user_id FROM posts JOIN users ON posts.user_id = users.id $search_sql ORDER BY posts.id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, SQLITE3_TEXT);
}
$stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
$stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
$results = $stmt->execute();

if (isset($_GET['view'])) {
    $view_id = intval($_GET['view']);
    $db->exec("UPDATE posts SET view_count = view_count + 1 WHERE id = $view_id");
    header("Location: home.php");
    exit;
}

if (isset($_GET['like'])) {
    $like_id = intval($_GET['like']);
    $check_like = $db->prepare("SELECT * FROM likes WHERE user_id = :user_id AND post_id = :post_id");
    $check_like->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $check_like->bindValue(':post_id', $like_id, SQLITE3_INTEGER);
    $existing = $check_like->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$existing) {
        $db->exec("UPDATE posts SET likes = likes + 1 WHERE id = $like_id");
        $insert_like = $db->prepare("INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)");
        $insert_like->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $insert_like->bindValue(':post_id', $like_id, SQLITE3_INTEGER);
        $insert_like->execute();
    }
    header("Location: home.php");
    exit;
}

$posts = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Gallery</title>
    <link rel="stylesheet" href="myapp.css">
</head>
<body>
<div class="container">
    <h1>Image Gallery</h1>

    <form method="get" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search title..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <p>
        <a href="upload.php">Upload Image</a> |
        <a href="logout.php">Logout</a>
    </p>

    <?php if (count($posts) === 0): ?>
        <p>No images found.</p>
    <?php else: ?>
        <div class="gallery-vertical">
            <?php foreach ($posts as $post): ?>
                <div class="gallery-item">
                    <a href="home.php?view=<?= $post['id'] ?>">
                        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                    </a>
                    <p><strong><?= htmlspecialchars($post['title']) ?></strong></p>
                    <p>👁️ <?= $post['view_count'] ?> | ❤️ <?= $post['likes'] ?></p>
                    <p>Posted by: 
                        <a href="profile.php?user_id=<?= $post['user_id'] ?>">
                            <?= htmlspecialchars($post['username']) ?>
                        </a>
                    </p>
                    <?php if (is_admin()): ?>
                        <a href="edit.php?id=<?= $post['id'] ?>">✏️ Edit</a>
                        <a href="delete.php?id=<?= $post['id'] ?>" onclick="return confirm('Delete this image?')">🗑️ Delete</a>
                    <?php endif; ?>
                    <a href="view_post.php?id=<?= $post['id'] ?>">View & Comment</a>

                    <form method="get" action="">
                        <input type="hidden" name="like" value="<?= $post['id'] ?>">
                        <button class="like-btn">❤️ Like</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 20px;">
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <?php if ($p == $page): ?>
                    <strong><?= $p ?></strong>
                <?php else: ?>
                    <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
