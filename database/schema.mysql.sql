CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    session_token VARCHAR(255) NULL,
    crypto_balance DECIMAL(20, 8) DEFAULT 0.00000000,
    location_lat DECIMAL(10, 8) NULL,
    location_lon DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO products (name, price, stock) VALUES
('Bitcoin Miner S19', 2500.00, 10),
('Hardware Wallet', 99.99, 50),
('Mining Rig Frame', 150.00, 25);
