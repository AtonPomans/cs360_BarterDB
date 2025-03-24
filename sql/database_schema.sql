CREATE DATABASE barter_db;
USE barter_db;

/* Users Table */
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* Items Table */
CREATE TABLE items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255),
    description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
