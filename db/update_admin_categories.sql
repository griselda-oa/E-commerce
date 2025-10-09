-- Update script for admin categories
-- This file contains database updates for admin functionality

USE shoppn;

-- Add is_admin column if it doesn't exist
ALTER TABLE customer ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) NOT NULL DEFAULT 0;

-- Add user_role column if it doesn't exist  
ALTER TABLE customer ADD COLUMN IF NOT EXISTS user_role INT(11) NOT NULL DEFAULT 2;

-- Create categories table if it doesn't exist
CREATE TABLE IF NOT EXISTS categories (
    cat_id INT(11) NOT NULL AUTO_INCREMENT,
    cat_name VARCHAR(100) NOT NULL,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (cat_id),
    FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE
);

-- Create products table if it doesn't exist
CREATE TABLE IF NOT EXISTS products (
    product_id INT(11) NOT NULL AUTO_INCREMENT,
    product_name VARCHAR(100) NOT NULL,
    product_description TEXT NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    product_stock INT(11) NOT NULL DEFAULT 0,
    cat_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (product_id),
    FOREIGN KEY (cat_id) REFERENCES categories(cat_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE
);

-- Update existing users to have proper roles
UPDATE customer SET user_role = 1, is_admin = 1 WHERE customer_id IN (1, 2);
UPDATE customer SET user_role = 2, is_admin = 0 WHERE customer_id IN (3, 4);
