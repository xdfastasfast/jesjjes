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
    if (isset($_GET['logout'])) {
        setcookie('user_logged_in', '', time() - 3600, "/");
        setcookie('username', 'guest', time() + 3600, "/"); 
        header('Location: glowna.php');
    }

    try {

        $db = getDatabaseConnection();
        $query = "SELECT p.id, p.title, p.content, p.created_at AS date, u.username AS author
                  FROM posts p JOIN users u ON p.user_id = u.id
                  ORDER BY p.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo 'Błąd połączenia z bazą danych: ' . $e->getMessage();
        exit;
    }

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
        <?php
        if (isset($posts[$displayedPostIndex - 1])) {
            echo '<a class="previous" href="?id=' . $posts[$displayedPostIndex - 1]['id'] . '">Previous</a>';
        }
        ?>
    </div>
    <div class="main-section">
    <div class="post" >
        <h2><a href="commentpostpage.php?id=<?php echo $displayedPost['id']; ?>"><?php echo $displayedPost['title'] ?></a></h2>
        <p><?php echo $displayedPost['content'] ?></p>
        <h5>Autor: <?php echo $displayedPost['author'] ?>    Data: <?php echo $displayedPost['date'] ?></h5>
    </div>
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