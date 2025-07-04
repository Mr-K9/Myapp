<?php
require_once 'includes/auth.php';
require_login();

$db = new SQLite3('myapp.db');
$user_id = $_SESSION['user']['id'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');
    $cover_file = $_FILES['cover'] ?? null;
    $cover_name = '';

    if ($cover_file && $cover_file['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($cover_file['type'], $allowedTypes)) {
            $cover_name = 'cover_' . $user_id . '_' . time() . '_' . basename($cover_file['name']);
            $cover_path = 'uploads/' . $cover_name;
            move_uploaded_file($cover_file['tmp_name'], $cover_path);
        } else {
            $error = 'Invalid cover image format.';
        }
    }

    if (!$error) {
        $query = "UPDATE users SET bio = :bio";
        if ($cover_name !== '') {
            $query .= ", cover = :cover";
        }
        $query .= " WHERE id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':bio', $bio, SQLITE3_TEXT);
        if ($cover_name !== '') {
            $stmt->bindValue(':cover', $cover_name, SQLITE3_TEXT);
        }
        $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();

        $success = 'Profile updated!';
    }
}

$user_stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$user_stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
$user = $user_stmt->execute()->fetchArray(SQLITE3_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="myapp.css">
</head>
<body>
<div class="container">
    <h2>Edit Profile</h2>

    <?php if ($error): ?>
        <p style="color:red;">❌ <?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p style="color:green;">✅ <?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="bio">Bio:</label><br>
        <textarea name="bio" rows="4" cols="50"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea><br><br>

        <label for="cover">Upload Cover Photo:</label><br>
        <input type="file" name="cover"><br><br>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="profile.php?user_id=<?= $user_id ?>">Back to Profile</a></p>
</div>
</body>
</html>
