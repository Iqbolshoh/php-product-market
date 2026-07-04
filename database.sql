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

-- 4. CARTS TABLE
CREATE TABLE carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- INSERTING USERS
INSERT INTO users (name, email, password, role) VALUES 
('Iqbolshoh Ilhomjonov', 'iilhomjonov777@gmail.com', '$2y$10$FK1CG7WYwBbjC/rNTscuGOuH05Jqs.fxLxYB0rZ..Y1keEoDiEQMu', 'admin'),
('Simple User', 'user@iqbolshoh.uz',  '$2y$10$FK1CG7WYwBbjC/rNTscuGOuH05Jqs.fxLxYB0rZ..Y1keEoDiEQMu',  'user');

-- INSERTING CATEGORIES
INSERT INTO categories (name) VALUES
('Electronics'),
('Books'),
('Clothing'),
('Home & Kitchen'),
('Sports & Outdoors');

-- INSERTING PRODUCTS (12 items)
INSERT INTO products (name, image_url, description, price, discount_price, category_id) VALUES
-- Electronics (4 items)
('Smartphone', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500', 'A high-end smartphone with a sleek design and advanced camera system.', 699.99, 649.99, 1),
('Laptop', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500', 'A powerful laptop for work and play with 16GB RAM.', 999.99, 899.99, 1),
('Wireless Headphones', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500', 'Premium noise-cancelling wireless headphones.', 199.99, 149.99, 1),
('Smart Watch', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500', 'Fitness tracker with heart rate monitor and GPS.', 299.99, 249.99, 1),

-- Books (2 items)
('Fiction Book', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=500', 'An engaging fiction book that captivates readers.', 19.99, 14.99, 2),
('Cookbook', 'https://images.unsplash.com/photo-1589998059171-988d887df646?w=500', 'Healthy recipes cookbook with 200+ recipes.', 24.99, 19.99, 2),

-- Clothing (3 items)
('T-Shirt', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500', 'A comfortable cotton t-shirt available in various sizes.', 14.99, 12.99, 3),
('Denim Jacket', 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=500', 'Classic denim jacket for all seasons.', 79.99, 64.99, 3),
('Running Shoes', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500', 'Lightweight running shoes with cushioning technology.', 129.99, 99.99, 3),

-- Home & Kitchen (2 items)
('Blender', 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=500', 'A versatile blender for smoothies and more.', 49.99, 39.99, 4),
('Coffee Maker', 'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=500', 'Programmable coffee maker with 12-cup capacity.', 89.99, 74.99, 4),

-- Sports & Outdoors (1 item)
('Yoga Mat', 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=500', 'A non-slip yoga mat for all your fitness needs.', 29.99, 22.99, 5);