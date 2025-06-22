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

$query = 'SELECT * FROM posts WHERE author = :username ORDER BY date DESC';
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $_COOKIE['username']);
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

if ($displayedPost === null) {
    $displayedPost = $posts[0];
    $displayedPostIndex = 0;
}

$previousPostId = $displayedPostIndex > 0 ? $posts[$displayedPostIndex - 1]['id'] : null;
$nextPostId = $displayedPostIndex < count($posts) - 1 ? $posts[$displayedPostIndex + 1]['id'] : null;
if (isset($_POST['delete_post_id'])) {
    $postIdToDelete = $_POST['delete_post_id'];

    try {
        $query = 'DELETE FROM posts WHERE id = :id AND author = :username';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $postIdToDelete);
        $stmt->bindParam(':username', $_COOKIE['username']);
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
        $author = isset($_COOKIE['username']) ? $_COOKIE['username'] : null;

        if (!$author) {
            throw new Exception('Author not found in cookies');
        }

        $date = date("Y-m-d H:i:s");

        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare("INSERT INTO posts (title, author, content, date) VALUES (?, ?, ?, ?)");

        $stmt->execute([$title, $author, $content, $date]);

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
        $author = isset($_COOKIE['username']) ? $_COOKIE['username'] : null;

        if (!$author) {
            throw new Exception('Author not found in cookies');
        }

        $date = date("Y-m-d H:i:s");

        $stmt = $db->prepare("UPDATE posts SET title = :title, content = :content, date = :date WHERE id = :id AND author = :author");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $postIdToUpdate);
        $stmt->bindParam(':author', $author);
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
        <nav class="postpage">
            <form action="mypostspage.php" method="post">
                <h1>EDIT POST</h1>
                <label for="title">Title:</label><br>
                <input type="text" id="title" name="title" value="<?php echo $displayedPost['title']; ?>"><br>
                <label for="content">Content:</label><br>
                <textarea id="content" name="content"><?php echo $displayedPost['content']; ?></textarea><br>
                <input type="hidden" name="update_post_id" value="<?php echo $displayedPost['id']; ?>">
                <input type="submit" value="Update">
            </form>
        </nav>
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