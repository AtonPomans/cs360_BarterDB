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
/*    description TEXT, */
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE barter_post (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    poster_id INT NOT NULL,
    partner_id INT,
    offered_item INT NOT NULL,
    requested_item INT NOT NULL,
    offered_quantity INT DEFAULT 1,
    requested_quantity INT DEFAULT 1,
    status ENUM('open', 'closed') DEFAULT 'open',
    FOREIGN KEY (poster_id) REFERENCES users(user_id),
    FOREIGN KEY (partner_id) REFERENCES users(user_id),
    FOREIGN KEY (offered_item) REFERENCES items(item_id),
    FOREIGN KEY (requested_item) REFERENCES items(item_id)
);
