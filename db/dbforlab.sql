-- Database for Lab
-- This file contains the original database structure

CREATE DATABASE IF NOT EXISTS ecommerce_2025A_griselda_owusu;
USE ecommerce_2025A_griselda_owusu;

-- Customer table
CREATE TABLE IF NOT EXISTS customer (
    customer_id INT(11) NOT NULL AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(50) NOT NULL UNIQUE,
    customer_pass VARCHAR(150) NOT NULL,
    customer_country VARCHAR(30) NOT NULL,
    customer_city VARCHAR(30) NOT NULL,
    customer_contact VARCHAR(15) NOT NULL,
    customer_image VARCHAR(100) DEFAULT NULL,
    user_role INT(11) NOT NULL DEFAULT 2,
    PRIMARY KEY (customer_id)
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    cat_id INT(11) NOT NULL AUTO_INCREMENT,
    cat_name VARCHAR(100) NOT NULL,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (cat_id),
    FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE
);

-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    brand_id INT(11) NOT NULL AUTO_INCREMENT,
    brand_name VARCHAR(100) NOT NULL,
    cat_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    UNIQUE KEY unique_brand_category (brand_name, cat_id),
    PRIMARY KEY (brand_id),
    FOREIGN KEY (cat_id) REFERENCES categories(cat_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT(11) NOT NULL AUTO_INCREMENT,
    product_title VARCHAR(100) NOT NULL,
    product_description TEXT NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    product_keyword VARCHAR(255) DEFAULT NULL,
    product_image VARCHAR(255) DEFAULT NULL,
    product_stock INT(11) NOT NULL DEFAULT 0,
    cat_id INT(11) NOT NULL,
    brand_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (product_id),
    FOREIGN KEY (cat_id) REFERENCES categories(cat_id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES brands(brand_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, customer_image, user_role) VALUES
('Griselda Owusu-Ansah', 'griselda.owusu@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ghana', 'Accra', '1234567890', NULL, 1),
('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'USA', 'New York', '0987654321', NULL, 2);

-- Insert artisan craft categories for Owusu Artisan Market
INSERT INTO categories (cat_name) VALUES
('Kente & Textiles'),
('Wood Carvings & Sculptures'),
('Handcrafted Jewelry'),
('Beaded Art & Accessories'),
('Ceramics & Pottery');

-- Insert artisan studios and craft workshops
INSERT INTO brands (brand_name) VALUES
('Bonwire Kente Weavers'),
('Ashanti Traditional Textiles'),
('Adinkra Artisans Guild'),
('Ghana Wood Masters'),
('Accra Bead Crafts'),
('Krobo Glass Beads'),
('Heritage Beadwork Studio'),
('Contemporary Craft Designs'),
('Volta Clay Works'),
('Royal Pottery Accra');

-- Insert artisan products with realistic Ghana pricing and stock levels
INSERT INTO products (product_title, product_price, product_keyword, product_image, product_stock) VALUES
-- Kente & Textiles
('Handwoven Kente Cloth (6 yards)', 280.00, 'kente cloth traditional african textile', NULL, 8),
('Kente Scarf with Adinkra Symbols', 95.00, 'kente scarf adinkra silk accessories', NULL, 25),
('Handwoven Kente Throw Pillow Set', 480.00, 'kente pillow throw handwoven home decor', NULL, 12),

-- Wood Carvings & Sculptures
('Carved Adinkra Symbol Wall Art', 750.00, 'adinkra wood carving wall art mahogany', NULL, 6),
('Hand-carved Tribal Mask', 850.00, 'wooden mask tribal ceremonial teak', NULL, 4),
('Carved Wooden Stools (Ase)', 550.00, 'wooden stool furniture traditional handcrafted', NULL, 10),

-- Handcrafted Jewelry
('Ghana Bead Necklace Set', 320.00, 'bead jewelry glass krobo handmade artisan', NULL, 18),
('Krobo Recycled Glass Beads', 85.00, 'glass beads krobo recycled eco-friendly', NULL, 35),
('Leather & Bead Bangle Set', 120.00, 'bangles leather beads handmade artisan', NULL, 28),

-- Beaded Art & Accessories
('Beaded Handbag with Adinkra Symbols', 420.00, 'handbag raffia beads adinkra eco-friendly', NULL, 12),
('Decorative Beaded Coasters', 95.00, 'coasters beads decorative traditional home', NULL, 40),
('Handwoven Raffia Basket Set', 95.00, 'baskets raffia woven traditional storage', NULL, 30),

-- Ceramics & Pottery
('Traditional Clay Cooking Pot', 65.00, 'clay pot cooking traditional pottery', NULL, 20),
('Decorative Ceramic Vase', 75.00, 'ceramic vase decorative adinkra pottery', NULL, 25),
('Ceramic Water Storage Jar', 120.00, 'ceramic water jar storage traditional', NULL, 15);