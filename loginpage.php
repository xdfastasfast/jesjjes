<!DOCTYPE html>
<html lang="pl">
<head>
<title>Chomiki - Logowanie</title>
<link rel="stylesheet" type="text/css" href="glowna.css">
<meta charset="UTF-8">
</head>
<body class="loginpage">
 
<header>
<h1>Klub Anonimowych Chomiczar</h1>
</header>
 
<div class="nav-container">
<nav>
<ul>
<li><a href="glowna.php">Main Page</a></li>
</ul>
</nav>
</div>
 
<main>
<?php
    include 'login.php';
    include 'database.php';
require_once('database.php');
 
    session_start();
 
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];
 
        try {
            $db = getDatabaseConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
if ($user && password_verify($password, $user['password'])) {

                 setcookie('user_logged_in', true, time() + 3600, "/");
                setcookie('username', $username, time() + 3600, "/");
                setcookie('user_id', $user['id'], time() + 3600, "/");
 
                header("Location: glowna.php");
                exit;
            } else {
                echo "<p style='color:red;'>Nieprawidłowy login lub hasło.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Błąd połączenia z bazą danych: " . $e->getMessage() . "</p>";
        }
    }
    ?>
 
    <form method="POST" action="loginpage.php">
<label for="username">Nazwa użytkownika:</label>
<input type="text" id="username" name="username" required>
 
        <label for="password">Hasło:</label>
<input type="password" id="password" name="password" required>
 
        <input type="submit" value="Zaloguj się">
</form>
</main>
 
<footer>
<p>&copy; 2025 Chomiki. Wszelkie prawa zastrzeżone.</p>
</footer>
 
</body>
</html>