<?php
include 'database.php';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_COOKIE['username']; // zakładamy, że masz ciasteczko
 
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("INSERT INTO posts (author, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$author, $title, $content]);
 
        echo "Post dodany!";
        header("Location: glowna.php");
        exit;
    } catch (PDOException $e) {
        echo "Błąd dodawania posta: " . $e->getMessage();
    }
}
?>