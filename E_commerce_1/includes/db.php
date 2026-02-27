<?php
// includes/db.php

// SQLite database file path
$db_file = __DIR__ . '/../ecommerce.sqlite';

// Check if we need to initialize the database
$initialize = !file_exists($db_file);

try {
    // Connect to SQLite
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Enable foreign keys
    $pdo->exec("PRAGMA foreign_keys = ON");

    // Initialize database if it didn't exist
    if ($initialize) {
        $sql = "
        -- Users Table
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100),
            email VARCHAR(150) UNIQUE,
            password VARCHAR(255),
            is_admin TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Categories Table
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );

        -- Products Table
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            category_id INT,
            image VARCHAR(255) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        );

        -- Orders Table
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INT,
            total DECIMAL(10,2),
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );

        -- Order Items Table
        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INT,
            product_id INT,
            quantity INT,
            price DECIMAL(10,2),
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );

        -- Seed Categories
        INSERT INTO categories (name) VALUES ('Clothing'), ('Electronics'), ('Books');

        -- Seed Products
        INSERT INTO products (name, description, price, category_id) VALUES
        ('Basic T‑shirt','Comfortable cotton t-shirt',12.99,1),
        ('Wireless Mouse','Ergonomic wireless mouse',25.50,2),
        ('Learn PHP','A beginner PHP book',19.00,3);

        -- Seed Admin User (password: admin123)
        INSERT INTO users (name, email, password, is_admin) VALUES
        ('Admin', 'admin@example.com', '$2y$10$u5Q0vJ7w5bZk1R9wq3tywevQx1k1cJuiqQ.I3W3Y2v2G0YfZQ1a3a', 1);
        ";

        // Execute the initialization SQL
        $pdo->exec($sql);
    }
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
?>