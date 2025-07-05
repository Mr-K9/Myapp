<?php
require_once 'includes/auth.php';
require_login();

$db = new SQLite3('myapp.db');

$viewer_id = $_SESSION['user']['id'];
$profile_user_id = intval($_GET['user_id'] ?? 0);
$self_profile = $viewer_id === $profile_user_id;

// Fetch user info
$user_stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$user_stmt->bindValue(':id', $profile_user_id, SQLITE3_INTEGER);
$user = $user_stmt->execute()->fetchArray(SQLITE3_ASSOC);

// Handle follow/unfollow
if (isset($_POST['follow_action']) && !$self_profile) {
    if ($_POST['follow_action'] === 'follow') {
        $stmt = $db->prepare("INSERT INTO follows (follower_id, following_id) VALUES (:follower, :following)");
    } else {
        $stmt = $db->prepare("DELETE FROM follows WHERE follower_id = :follower AND following_id = :following");
    }
    $stmt->bindValue(':follower', $viewer_id, SQLITE3_INTEGER);
    $stmt->bindValue(':following', $profile_user_id, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: profile.php?user_id=$profile_user_id");
    exit;
}

// Check if already following
$follow_check = $db->prepare("SELECT 1 FROM follows WHERE follower_id = :viewer AND following_id = :user");
$follow_check->bindValue(':viewer', $viewer_id, SQLITE3_INTEGER);
$follow_check->bindValue(':user', $profile_user_id, SQLITE3_INTEGER);
$is_following = $follow_check->execute()->fetchArray() ? true : false;

// Fetch user posts
$post_stmt = $db->prepare("SELECT * FROM posts WHERE user_id = :id ORDER BY id DESC");
$post_stmt->bindValue(':id', $profile_user_id, SQLITE3_INTEGER);
$results = $post_stmt->execute();

$posts = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($user['username']) ?>'s Profile</title>
  <link rel="stylesheet" href="myapp.css">
  <style>
    .profile-cover {
      height: 200px;
      background: url('uploads/<?= $user['cover'] ?? 'cover.jpg' ?>') center/cover no-repeat;
      border-radius: 10px 10px 0 0;
      position: relative;
    }
    .profile-avatar {
      position: absolute;
      bottom: -30px;
      left: 20px;
      background: #fff;
      padding: 10px 20px;
      border-radius: 50px;
      font-weight: bold;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .profile-info-section {
      padding: 40px 20px 20px;
      background: #f9f9f9;
      border-radius: 0 0 10px 10px;
      text-align: center;
    }
    .bio { color: #555; margin: 10px 0; }
    .follow-btn, .edit-btn {
      padding: 8px 16px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 20px;
      font-weight: bold;
      cursor: pointer;
      margin: 5px;
    }
    .tabs { margin-top: 20px; display: flex; justify-content: center; gap: 10px; }
    .tab-link { padding: 10px 20px; background: #eee; border: none; border-radius: 10px; cursor: pointer; }
    .tab-link.active { background: #007bff; color: white; }
    .tab-content { display: none; padding: 20px; }
    .tab-content.active { display: block; }
    .profile-gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    .profile-post-card {
      background: #ffffff;
      border: 1px solid #eee;
      border-radius: 10px;
      padding: 10px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      transition: transform 0.3s;
    }
    .profile-post-card:hover { transform: scale(1.02); }
    .profile-post-card img {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="profile-cover">
    <div class="profile-avatar">@<?= htmlspecialchars($user['username']) ?></div>
  </div>

  <div class="profile-info-section">
    <h2><?= htmlspecialchars($user['username']) ?></h2>
    <p class="bio">Bio: <?= nl2br(htmlspecialchars($user['bio'] ?? 'No bio yet.')) ?></p>

    <?php if ($self_profile): ?>
      <a href="edit_profile.php" class="edit-btn">✏️ Edit Profile</a>
    <?php else: ?>
      <form method="post" style="display:inline;">
        <input type="hidden" name="follow_action" value="<?= $is_following ? 'unfollow' : 'follow' ?>">
        <button type="submit" class="follow-btn">
          <?= $is_following ? '✔ Following' : '+ Follow' ?>
        </button>
      </form>
    <?php endif; ?>
  </div>

  <div class="tabs">
    <button class="tab-link active" onclick="showTab('posts')">Posts</button>
    <button class="tab-link" onclick="showTab('about')">About</button>
  </div>

  <div id="posts" class="tab-content active">
    <?php if (count($posts) === 0): ?>
      <p>This user has not posted yet.</p>
    <?php else: ?>
      <div class="profile-gallery">
        <?php foreach ($posts as $post): ?>
          <div class="profile-post-card">
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
            <p><strong><?= htmlspecialchars($post['title']) ?></strong></p>
            <p>👁️ <?= $post['view_count'] ?> | ❤️ <?= $post['likes'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div id="about" class="tab-content">
    <p>User ID: <?= $user['id'] ?></p>
    <p>Total Posts: <?= count($posts) ?></p>
    <p>Email: <?= htmlspecialchars($user['email'] ?? '-') ?></p>
    <p>Joined: Not tracked</p>
  </div>
</div>

<script>
function showTab(id) {
  document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  event.target.classList.add('active');
}
</script>
</body>
</html>
