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

?>

<header>
    <h1>Klub Anonimowych Chomiczar</h1>
</header>

<div class="nav-container">
    <nav>
        <ul>
            <li><a href="glowna.php">Main Page</a></li>
            <?php if (isUserLoggedIn()) : ?>
                <li><a href="accountpage.php">Account</a></li>
                <li class="logout-link"><a href="?logout=true">Logout</a></li>
            <?php else : ?>
                <li class="login-link"><a href="loginpage.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php
$db = getDatabaseConnection();

$query = 'SELECT p.id, p.title, p.content, p.created_at AS date, u.username AS author
          FROM posts p JOIN users u ON p.user_id = u.id
          WHERE p.user_id = :uid ORDER BY p.created_at DESC';
$stmt = $db->prepare($query);
$stmt->bindParam(':uid', $_COOKIE['user_id']);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$requestedPostId = isset($_GET['id']) ? $_GET['id'] : null;

$displayedPost = null;
$displayedPostIndex = 0;
foreach ($posts as $index => $post) {
    if ($post['id'] == $requestedPostId) {
        $displayedPost = $post;
        $displayedPostIndex = $index;
        break;
    }
}

if ($displayedPost === null && !empty($posts)) {
    $displayedPost = $posts[0];
    $displayedPostIndex = 0;
}

$previousPostId = $displayedPostIndex > 0 ? $posts[$displayedPostIndex - 1]['id'] : null;
$nextPostId = $displayedPostIndex < count($posts) - 1 ? $posts[$displayedPostIndex + 1]['id'] : null;
if (isset($_POST['delete_post_id'])) {
    $postIdToDelete = $_POST['delete_post_id'];

    try {
        $query = 'DELETE FROM posts WHERE id = :id AND user_id = :uid';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $postIdToDelete);
        $stmt->bindParam(':uid', $_COOKIE['user_id']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo 'Post was successfully deleted';
        } else {
            echo 'Could not delete post. Either the post does not exist, or you are not the author of the post';
        }
    } catch (PDOException $e) {
        echo 'Database error: ' . $e->getMessage();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_post'])) {
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;

    try {
        $userId = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;

        if (!$userId) {
            throw new Exception('User not found in cookies');
        }

        $date = date("Y-m-d H:i:s");

        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, ?)");

        $stmt->execute([$userId, $title, $content, $date]);

        echo "Post successfully added.";
        header("Location: " . $_SERVER['PHP_SELF'], true, 303);
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_post_id'])) {
    $postIdToUpdate = $_POST['update_post_id'];
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;

    try {
        $userId = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;

        if (!$userId) {
            throw new Exception('User not found in cookies');
        }

        $date = date("Y-m-d H:i:s");

        $stmt = $db->prepare("UPDATE posts SET title = :title, content = :content, created_at = :date WHERE id = :id AND user_id = :uid");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $postIdToUpdate);
        $stmt->bindParam(':uid', $userId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo 'Post was successfully updated';
        } else {
            echo 'Could not update post. Either the post does not exist, or you are not the author of the post';
        }

        header("Location: " . $_SERVER['PHP_SELF'], true, 303);
        exit;
    } catch (PDOException $e) {
        echo 'Database error: ' . $e->getMessage();
    }
}

?>
<main>
    <div class="main-section">
        <?php
        if (isset($posts[$displayedPostIndex - 1])) {
            echo '<a class="previous" href="?id=' . $posts[$displayedPostIndex - 1]['id'] . '">Previous</a>';
        }
        ?>
    </div>
    <div class="main-section">
        <nav class="postpage">
            <form action="mypostspage.php" method="post">
                <h1>ADD POST</h1>
                <label for="title">Title:</label><br>
                <input type="text" id="title" name="title"><br>
                <label for="content">Content:</label><br>
                <textarea id="content" name="content"></textarea><br>
                <input type="hidden" name="add_post" value="1">
                <input class="dodpost" type="submit" value="Submit">
            </form>
        </nav>
    </div>
    <div class="main-section">
        <div class="post">
            <?php if ($displayedPost !== null) : ?>
                <div class="postincomment">
                    <h2><a href="commentpostpage.php?id=<?php echo $displayedPost['id']; ?>"><?php echo $displayedPost['title'] ?></a></h2>
                    <p><?php echo $displayedPost['content'] ?></p>
                    <h5>Autor: <?php echo $displayedPost['author'] ?>    Data: <?php echo $displayedPost['date'] ?></h5>
                    <form method="post" action="mypostspage.php">
                        <input type="hidden" name="delete_post_id" value="<?php echo $displayedPost['id']; ?>">
                        <input type="submit" value="Delete">
                    </form>
                </div>
            <?php else : ?>
                <p>No posts to display.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="main-section">
        <?php if ($displayedPost !== null) : ?>
        <nav class="postpage">
            <form action="mypostspage.php" method="post">
                <h1>EDIT POST</h1>
                <label for="title">Title:</label><br>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($displayedPost['title'], ENT_QUOTES); ?>"><br>
                <label for="content">Content:</label><br>
                <textarea id="content" name="content"><?php echo htmlspecialchars($displayedPost['content'], ENT_QUOTES); ?></textarea><br>
                <input type="hidden" name="update_post_id" value="<?php echo $displayedPost['id']; ?>">
                <input type="submit" value="Update">
            </form>
        </nav>
        <?php endif; ?>
    </div>
    <div class="main-section">
        <?php
        if (isset($posts[$displayedPostIndex + 1])) {
            echo '<a class="next" href="?id=' . $posts[$displayedPostIndex + 1]['id'] . '">Next</a>';
        }
        ?>
    </div>
</main>
<footer>
    <p>&copy; 2025 Chomiki. Wszelkie prawa zastrzeżone.</p>
</footer>
</body>
</html>