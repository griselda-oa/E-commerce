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

-- Insert comprehensive artisan categories for Owusu Artisan Market
INSERT INTO categories (cat_name) VALUES
('Kente & Textiles'),
('Wood Carvings & Sculptures'),
('Handcrafted Jewelry'),
('Beaded Art & Accessories'),
('Ceramics & Pottery'),
('Leather Goods & Accessories'),
('Metalwork & Brass Art'),
('Basketry & Woven Items'),
('Traditional Musical Instruments'),
('Home Decor & Furnishings'),
('Fashion & Clothing'),
('Sculptures & Figurines'),
('Paintings & Wall Art'),
('Garden & Outdoor Items'),
('Kitchen & Dining Items');

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

-- Insert artisan products with realistic Ghana pricing
INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) VALUES
-- Kente & Textiles
(1, 1, 'Handwoven Kente Cloth (6 yards)', 280.00, 'Authentic handwoven Kente cloth from Bonwire, perfect for traditional ceremonies', NULL, 'kente, traditional, handwoven, cloth'),
(1, 2, 'Kente Scarf with Adinkra Symbols', 95.00, 'Elegant Kente scarf featuring traditional Adinkra symbols', NULL, 'kente, scarf, adinkra, symbols'),
(1, 1, 'Handwoven Kente Throw Pillow Set', 480.00, 'Set of 2 decorative pillows made from authentic Kente fabric', NULL, 'kente, pillows, decorative, home'),

-- Wood Carvings & Sculptures
(2, 4, 'Carved Adinkra Symbol Wall Art', 750.00, 'Hand-carved wooden wall art featuring traditional Adinkra symbols', NULL, 'wood, carving, adinkra, wall art'),
(2, 4, 'Hand-carved Tribal Mask', 850.00, 'Authentic tribal mask carved from local Ghanaian wood', NULL, 'mask, tribal, wood carving, traditional'),
(2, 4, 'Carved Wooden Stools (Ase)', 550.00, 'Traditional Akan stools hand-carved by master craftsmen', NULL, 'stools, wood, traditional, ase'),

-- Handcrafted Jewelry
(3, 5, 'Ghana Bead Necklace Set', 320.00, 'Beautiful set of traditional Ghanaian bead necklaces', NULL, 'beads, necklace, jewelry, traditional'),
(3, 6, 'Krobo Recycled Glass Beads', 85.00, 'Eco-friendly glass beads made from recycled materials', NULL, 'glass beads, recycled, krobo, eco'),
(3, 5, 'Leather & Bead Bangle Set', 120.00, 'Handcrafted leather bangles adorned with traditional beads', NULL, 'bangles, leather, beads, handcrafted'),

-- Beaded Art & Accessories
(4, 7, 'Beaded Handbag with Adinkra Symbols', 420.00, 'Stylish handbag featuring intricate beadwork and Adinkra symbols', NULL, 'handbag, beads, adinkra, fashion'),
(4, 8, 'Decorative Beaded Coasters', 95.00, 'Set of 6 decorative coasters with traditional bead patterns', NULL, 'coasters, beads, decorative, home'),
(4, 8, 'Handwoven Raffia Basket Set', 95.00, 'Set of 3 handwoven raffia baskets for home storage', NULL, 'baskets, raffia, handwoven, storage'),

-- Ceramics & Pottery
(5, 9, 'Traditional Clay Cooking Pot', 65.00, 'Authentic clay cooking pot used in traditional Ghanaian cuisine', NULL, 'clay pot, cooking, traditional, ceramic'),
(5, 10, 'Decorative Ceramic Vase', 75.00, 'Beautiful handcrafted ceramic vase with traditional patterns', NULL, 'vase, ceramic, decorative, pottery'),
(5, 9, 'Ceramic Water Storage Jar', 120.00, 'Large ceramic jar for water storage, handcrafted locally', NULL, 'water jar, ceramic, storage, pottery');