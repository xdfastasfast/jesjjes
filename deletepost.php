<?php
include_once 'database.php';

if (!isset($_POST['post_id'])) {
    die('No post ID provided for deletion');
}

$postIdToDelete = $_POST['post_id'];

try {
    $db = getDatabaseConnection();
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

header('Location: mypostspage.php');
?>
