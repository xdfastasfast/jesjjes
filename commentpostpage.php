<!DOCTYPE html>
<html>
<head>
<title>Chomiki</title>
<link rel="stylesheet" type="text/css" href="glowna.css">
<meta charset="UTF-8">
</head>
<body>
 
<?php
include 'login.php';
include 'database.php';
logOut();
 
try {
    $db = getDatabaseConnection();
 
    if (isset($_GET['id'])) {
        $postId = $_GET['id'];
        $query = "SELECT * FROM posts WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$post) {
            die('Post not found.');
        }
 
        // Pobierz nazwę autora posta
        $authorQuery = "SELECT username FROM users WHERE id = ?";
        $authorStmt = $db->prepare($authorQuery);
        $authorStmt->execute([$post['user_id']]);
        $author = $authorStmt->fetchColumn();
 
    } else {
        header('Location: glowna.php');
        exit;
    }
} catch(PDOException $e) {
    echo 'Błąd połączenia z bazą danych: ' . $e->getMessage();
    exit;
}
?>
 
<header>
<h1>Klub Anonimowych Chomiczar</h1>
</header>
 
<div class="nav-container">
<nav>
<ul>
<li><a href="glowna.php">Main Page</a></li>
<?php if (isUserLoggedIn()) : ?>
<li><a href="mypostspage.php">My Posts</a></li>
<li><a href="accountpage.php">Account</a></li>
<li class="logout-link"><a href="?logout=true">Logout</a></li>
<?php else : ?>
<li class="login-link"><a href="loginpage.php">Login</a></li>
<?php endif; ?>
</ul>
</nav>
</div>
 
<main>
<div class="main-section">
<div class="comment">
<?php
            $query = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC LIMIT 3";
            $stmt = $db->prepare($query);
            $stmt->execute([$postId]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
            foreach ($comments as $comment) {
                // Pobierz nazwę użytkownika z user_id
                $userStmt = $db->prepare("SELECT username FROM users WHERE id = ?");
                $userStmt->execute([$comment['user_id']]);
                $username = $userStmt->fetchColumn();
 
                $author = $username ?: '[brak autora]';
                $content = isset($comment['comment']) ? htmlspecialchars($comment['comment']) : '[brak treści]';
                $date = isset($comment['created_at']) ? $comment['created_at'] : '[brak daty]';
 
                echo '<div class="commentinvidual">';
                echo "<h5>$author - $date</h5>";
                echo "<p>$content</p>";
                echo '</div>';
            }
            ?>
</div>
</div>
 
    <div class="main-section">
<div class="post">
<div class="postincomment">
<h2><?php echo htmlspecialchars($post['title'] ?? '[brak tytułu]'); ?></h2>
<p><?php echo htmlspecialchars($post['content'] ?? '[brak treści]'); ?></p>
<h5>Autor: <?php echo htmlspecialchars($author); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Data: <?php echo $post['created_at'] ?? '[brak daty]'; ?></h5>
</div>
</div>
</div>
 
    <div class="main-section">
<div class="comment-section">
<h3>Add a Comment</h3>
<form method="POST" action="add_comment.php">
<input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
<input type="hidden" name="user_id" value="<?php echo $_COOKIE['user_id'] ?? 0; ?>">
<div>
<label for="content">Comment:</label>
<textarea id="content" name="content" maxlength="200" required></textarea>
</div>
<div>
<input type="submit" value="Submit">
</div>
</form>
</div>
</div>
</main>
 
<footer>
<p>&copy; 2025 Chomiki. Wszelkie prawa zastrzeżone.</p>
</footer>
 
</body>
</html>