-- DROP DATABASE IF EXISTS
DROP DATABASE IF EXISTS product_market_db;

-- CREATE DATABASE
CREATE DATABASE product_market_db;
USE product_market_db;

-- 1. USERS TABLE
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. CATEGORIES TABLE
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. PRODUCTS TABLE
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    image_url VARCHAR(255),
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- INSERTING USERS
INSERT INTO users (name, email, password, role) VALUES 
('Iqbolshoh Ilhomjonov', 'iilhomjonov777@gmail.com', '$2y$10$FK1CG7WYwBbjC/rNTscuGOuH05Jqs.fxLxYB0rZ..Y1keEoDiEQMu', 'admin'),
('Simple User', 'user@iqbolshoh.uz',  '$2y$10$FK1CG7WYwBbjC/rNTscuGOuH05Jqs.fxLxYB0rZ..Y1keEoDiEQMu',  'user');