<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbname = "`".str_replace("`","``",DB_NAME)."`";
    $pdo->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->query("use $dbname");

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )");

    // Create posts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        post_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

    // Create tags table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tags (
        tag_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) UNIQUE NOT NULL
    )");

    // Create post_tags association table
    $pdo->exec("CREATE TABLE IF NOT EXISTS post_tags (
        post_id INT NOT NULL,
        tag_id INT NOT NULL,
        PRIMARY KEY (post_id, tag_id),
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
        FOREIGN KEY (tag_id) REFERENCES tags(tag_id)
    )");

    // Create homepage table
    $pdo->exec("CREATE TABLE IF NOT EXISTS homepage (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL
    )");

    //echo "Database connection and table creation successful";
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
