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

CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    post1_id INT NOT NULL,
    post2_id INT NOT NULL,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    item1_id INT NOT NULL,
    item2_id INT NOT NULL,
    hash_code VARCHAR(16) NOT NULL,
    part_a_code VARCHAR(8) NOT NULL,
    part_y_code VARCHAR(8) NOT NULL,
    p_sent BOOLEAN DEFAULT 0,
    e_sent BOOLEAN DEFAULT 0,
    is_complete BOOLEAN DEFAULT 0,
    cost_deduction DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (post1_id) REFERENCES barter_post(post_id),
    FOREIGN KEY (post2_id) REFERENCES barter_post(post_id),
    FOREIGN KEY (user1_id) REFERENCES users(user_id),
    FOREIGN KEY (user2_id) REFERENCES users(user_id),
    FOREIGN KEY (item1_id) REFERENCES items(item_id),
    FOREIGN KEY (item2_id) REFERENCES items(item_id)
);
