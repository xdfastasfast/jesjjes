<?php
include_once 'database.php';

if (!isset($_POST['post_id'])) {
    die('No post ID provided for deletion');
}

$postIdToDelete = $_POST['post_id'];

try {
    $db = getDatabaseConnection();
    $query = 'DELETE FROM posts WHERE id = :id AND user_id = :uid';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $postIdToDelete);
    $stmt->bindParam(':uid', $_COOKIE['user_id']);
    $stmt->execute();

    if ($stmt->rowCount() <= 0) {
        // Optional: handle case when deletion did not occur
    }
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
}

header('Location: mypostspage.php');
?>
