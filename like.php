<?php
require_once 'includes/auth.php';
require_login();

$db = new SQLite3('myapp.db');
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'];
$post_id = intval($_GET['id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

// Check if already liked
$stmt = $db->prepare("SELECT * FROM likes WHERE user_id = :user AND post_id = :post");
$stmt->bindValue(':user', $user_id, SQLITE3_INTEGER);
$stmt->bindValue(':post', $post_id, SQLITE3_INTEGER);
$liked = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if ($liked) {
    echo json_encode(['success' => false, 'message' => 'Already liked']);
    exit;
}

// Insert like
$db->exec("UPDATE posts SET likes = likes + 1 WHERE id = $post_id");
$stmt = $db->prepare("INSERT INTO likes (user_id, post_id) VALUES (:user, :post)");
$stmt->bindValue(':user', $user_id, SQLITE3_INTEGER);
$stmt->bindValue(':post', $post_id, SQLITE3_INTEGER);
$stmt->execute();

// Return updated like count
$result = $db->querySingle("SELECT likes FROM posts WHERE id = $post_id");
echo json_encode(['success' => true, 'likes' => $result]);
