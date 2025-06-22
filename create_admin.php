<?php

include 'database.php';
 
try {

    $db = getDatabaseConnection();
 
    $username = 'admin@example.com';

    $password = 'admin123';

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
 

    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");

    $stmt->execute([$username]);

    if ($stmt->fetch()) {

        echo "Użytkownik '$username' już istnieje.";

        exit;

    }
 

    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

    $stmt->execute([$username, $hashedPassword]);
 
    echo "Utworzono użytkownika '$username' z hasłem '$password'.";

} catch (PDOException $e) {

    echo "Błąd bazy danych: " . $e->getMessage();

}

?>

 