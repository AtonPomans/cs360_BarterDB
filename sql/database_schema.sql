CREATE DATABASE IF NOT EXISTS barter_db;
USE barter_db;

/* Users Table */
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* Items Table */
CREATE TABLE IF NOT EXISTS items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255),
    description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS barter_post (
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

CREATE TABLE IF NOT EXISTS transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    post1_id INT NOT NULL,
    post2_id INT NOT NULL,
    user_a_id INT NOT NULL,  -- A: requester of item P
    user_b_id INT NOT NULL,  -- B: A’s partner, sends item E
    user_x_id INT NOT NULL,  -- X: owner of item P
    user_y_id INT NOT NULL,  -- Y: X’s partner, receives item E
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
    FOREIGN KEY (user_a_id) REFERENCES users(user_id),
    FOREIGN KEY (user_b_id) REFERENCES users(user_id),
    FOREIGN KEY (user_x_id) REFERENCES users(user_id),
    FOREIGN KEY (user_y_id) REFERENCES users(user_id),
    FOREIGN KEY (item1_id) REFERENCES items(item_id),
    FOREIGN KEY (item2_id) REFERENCES items(item_id)
);

/* test users so no need to re-register (password same as name) */
INSERT INTO users (name, email, password_hash)
VALUES
('aaa', 'aaa@aaa.com', '$2y$10$o19a0uY.KizfX/qoIaz62uabNDnBEcsH1akkrA.vPuNJEXkWJg8OS'),
('bbb', 'bbb@bbb.com', '$2y$10$2Z1x9JmlPqtT5QjEnSC0TODLQeDmxPMd0IynydDLzLaz57NzVVJGu'),
('ccc', 'ccc@ccc.com', '$2y$10$2l83k0pLjbQrvoQP3K2q7.fY2VnUd6tpcOVNMIIi15SeHSxd1OqdW'),
('ddd', 'ddd@ddd.com', '$2y$10$2y61H.gegwQR/iRqqbVTtej7RYcXThVnd0w3yES6CGLgkzTVQHeX.'),
('eee', 'eee@eee.com', '$2y$10$E1.VKsiKSOOF2G6HK9cYIuh7BF865zWRs6Zt.aYB00/v7gEGH6tqW'),
('www', 'www@www.com', '$2y$10$Qe1mVP.lWb/GqPfMcHZuyuJZmBPjCWLO6fOMRv9pGZqfswtT/8ziy'),
('xxx', 'xxx@xxx.com', '$2y$10$xz/hwO/6IgJ338ijiRWPV.Dt0Mi97A27cMNrhyBkelMhSZrsVwTcu'),
('yyy', 'yyy@yyy.com', '$2y$10$0JtFHDd6.1qEJeuqcg9izeeDfHmfrdnNXHWKNA1R4mqg1nYSW9XCm'),
('zzz', 'zzz@zzz.com', '$2y$10$ftxQyHuVBVAIBXjtu3Yq7.7ci/Xqk3I3.YFHGBKYM41ZQNE4o5PoK');
