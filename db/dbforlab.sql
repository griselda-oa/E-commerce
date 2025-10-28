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
INSERT INTO categories (cat_name, user_id) VALUES
('Kente & Textiles', 2),
('Wood Carvings & Sculptures', 2),
('Handcrafted Jewelry', 2),
('Beaded Art & Accessories', 2),
('Ceramics & Pottery', 2);

-- Insert artisan studios and craft workshops
INSERT INTO brands (brand_name, cat_id, user_id) VALUES
('Bonwire Kente Weavers', 1, 2),
('Ashanti Traditional Textiles', 1, 2),
('Adinkra Artisans Guild', 2, 2),
('Ghana Wood Masters', 2, 2),
('Accra Bead Crafts', 3, 2),
('Krobo Glass Beads', 3, 2),
('Heritage Beadwork Studio', 4, 2),
('Contemporary Craft Designs', 4, 2),
('Volta Clay Works', 5, 2),
('Royal Pottery Accra', 5, 2);

-- Insert artisan products with realistic Ghana pricing and stock levels
INSERT INTO products (product_title, product_description, product_price, product_keyword, product_image, cat_id, brand_id, user_id, product_stock) VALUES
-- Kente & Textiles
('Handwoven Kente Cloth (6 yards)', 'Authentic Bonwire Kente, handwoven cotton, traditional Akan patterns, ceremonial quality', 280.00, 'kente cloth traditional african textile', NULL, 1, 1, 2, 8),
('Kente Scarf with Adinkra Symbols', 'Silk blend scarf, traditional Kente patterns with embroidered Adinkra symbols', 95.00, 'kente scarf adinkra silk accessories', NULL, 1, 2, 2, 25),
('Handwoven Kente Throw Pillow Set', 'Authentic Kente fabric, handwoven in Bonwire, set of 2 premium pillow covers', 480.00, 'kente pillow throw handwoven home decor', NULL, 1, 1, 2, 12),

-- Wood Carvings & Sculptures
('Carved Adinkra Symbol Wall Art', 'Traditional Ghanaian symbols carved on mahogany wood, 18x24 inches, signed by master artisan', 750.00, 'adinkra wood carving wall art mahogany', NULL, 2, 3, 2, 6),
('Hand-carved Tribal Mask', 'Traditional ceremonial mask, hand-carved from teak wood, one-of-a-kind piece', 850.00, 'wooden mask tribal ceremonial teak', NULL, 2, 3, 2, 4),
('Carved Wooden Stools (Ase)', 'Pair of traditional Ghanaian stools, mahogany wood, hand-carved detail work', 550.00, 'wooden stool furniture traditional handcrafted', NULL, 2, 4, 2, 10),

-- Handcrafted Jewelry
('Ghana Bead Necklace Set', 'Hand-strung glass bead necklace and earring set, unique artisan design from Krobo', 320.00, 'bead jewelry glass krobo handmade artisan', NULL, 3, 6, 2, 18),
('Krobo Recycled Glass Beads', 'Eco-friendly recycled glass beads, colorful traditional patterns', 85.00, 'glass beads krobo recycled eco-friendly', NULL, 3, 6, 2, 35),
('Leather & Bead Bangle Set', 'Handmade leather bangles with decorative bead details, set of 3', 120.00, 'bangles leather beads handmade artisan', NULL, 3, 5, 2, 28),

-- Beaded Art & Accessories
('Beaded Handbag with Adinkra Symbols', 'Sustainable raffia palm fiber, unique geometric patterns, artisan crafted with traditional beads', 420.00, 'handbag raffia beads adinkra eco-friendly', NULL, 4, 8, 2, 12),
('Decorative Beaded Coasters', 'Set of 6, traditional Ghanaian bead patterns, hand-woven technique', 95.00, 'coasters beads decorative traditional home', NULL, 4, 7, 2, 40),
('Handwoven Raffia Basket Set', 'Set of 5 traditional grass storage baskets, organic materials, various sizes, beaded accents', 95.00, 'baskets raffia woven traditional storage', NULL, 4, 8, 2, 30),

-- Ceramics & Pottery
('Traditional Clay Cooking Pot', 'Hand-thrown clay pot, suitable for traditional cooking, authentic Volta style', 65.00, 'clay pot cooking traditional pottery', NULL, 5, 9, 2, 20),
('Decorative Ceramic Vase', 'Hand-glazed ceramic vase with Adinkra symbols, 12 inches tall', 75.00, 'ceramic vase decorative adinkra pottery', NULL, 5, 10, 2, 25),
('Ceramic Water Storage Jar', 'Traditional water storage vessel, hand-made, 5-gallon capacity, decorative patterns', 120.00, 'ceramic water jar storage traditional', NULL, 5, 9, 2, 15);