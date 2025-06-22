<?php
include "database.php";
 
try {
    $db = getDatabaseConnection();
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $userId = $_POST['user_id'];
    $content = $_POST['content'];
 
    try {
        $query = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$postId, $userId, $content]);
        echo "Komentarz został dodany!";
        header("Location: commentpostpage.php?id=$postId");
        exit;
    } catch (PDOException $e) {
        echo "Błąd przy dodawaniu komentarza: " . $e->getMessage();
    }
}
?>