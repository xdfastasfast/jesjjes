
-- Tworzenie bazy danych
CREATE DATABASE IF NOT EXISTS projekt;
USE projekt;

-- Tabela użytkowników
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tabela postów
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela komentarzy
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Przykładowy użytkownik (login: test, hasło: test - zahashowane)
INSERT INTO users (username, password)
VALUES ('test', '$2y$10$123456789012345678901uM5PLF6jD3XOfiRL/z/rVzGpjFKPuH7y'); -- hasło test

-- Przykładowy post
INSERT INTO posts (user_id, content, image_path)
VALUES (1, 'To jest przykładowy post', 'PHOTOS/1.jpg');

-- Przykładowy komentarz
INSERT INTO comments (post_id, user_id, comment)
VALUES (1, 1, 'To jest przykładowy komentarz');
