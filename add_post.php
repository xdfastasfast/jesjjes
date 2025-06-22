<?php
include 'database.php';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $userId = $_COOKIE['user_id'];
 
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $title, $content]);
 
        header("Location: glowna.php");
        exit;
    } catch (PDOException $e) {
        echo "Błąd dodawania posta: " . $e->getMessage();
    }
}
?>